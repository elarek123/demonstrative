from asyncio import gather
from gc import callbacks
from warnings import filterwarnings

from telegram import (
    Update, CallbackQuery,
)
from telegram.constants import ParseMode
from telegram.ext import (
    CommandHandler,
    MessageHandler,
    CallbackQueryHandler,
    filters,
    CallbackContext, ContextTypes, ConversationHandler
)
from loader import app


from states import *
from config import  BOT_TOKEN
from config import REDIS_CHANNEL
from loader import redis_client
from redischan import reader
from telegram import ReplyKeyboardRemove, InlineKeyboardMarkup, InlineKeyboardButton
import asyncio
from Api import *
import httpx
import json
TOKEN = BOT_TOKEN

redis_pubsub = redis_client.pubsub()




async def starter(context: ContextTypes.DEFAULT_TYPE):
    await  redis_pubsub.subscribe(REDIS_CHANNEL)




async def consume_redis_messages(context: ContextTypes.DEFAULT_TYPE) -> None:
    message = await redis_pubsub.get_message(ignore_subscribe_messages=True)

    if message:
        await reader(message, context)




def pageButtons(data):
    reply_markup = []
    if data['prev']:
        reply_markup.append(
            InlineKeyboardButton(
                text='Предыдущий',
                callback_data='link_' + data['prev']
            )
        )
    if data['next']:
        reply_markup.append(
            InlineKeyboardButton(
                text='Следующий',
                callback_data='link_' + data['next']
            )
        )
    return reply_markup



def linkData(u: Update, c: CallbackContext, params = '', hders = None):
    if hders is None:
        hders = {}
    data = []
    if 'link' in c.user_data.keys() and 'link' in c.user_data['link']:
        print('LINK:' + c.user_data['link'].split('_')[-1] + params)
        data = httpx.get(c.user_data['link'].split('_')[-1] + params, headers=hders).json()
    return data



async def geoPager(u: Update, c: CallbackContext):
    data = linkData(u, c)
    reply_markup = []
    text = ''
    if len(data) == 0:
        data = getGeos().json()
    if data:
        geos = data['data']
        links = data['links']
        reply_markup.extend(
            [[InlineKeyboardButton(
                text=i['name'],
                callback_data='geo_id_' + str(i['id'])
            )]
            for i in geos])

        buttons = pageButtons(links)
        reply_markup.append(buttons)
        text += 'Выберите гео'
    else:
        text += 'Гео не найдены'
    reply_markup.append(menuButton())
    await sendResolver(c, u, text=text, reply_markup=InlineKeyboardMarkup(reply_markup))




def menuButton():
    reply_markup = [InlineKeyboardButton(
            text="Меню",
            callback_data='menu'
        )]
    return reply_markup

def productCardButtons(data, c: CallbackContext):
    reply_markup = []
    text = ''
    token = c.user_data.get('token', '')
    if data['data']:
        products = data['data'][0]
        links = data['links']
        text += f'Название: {products['product']['name']} \n'
        text += f'Цена: {products['final_price']}'
        if token:
            if products['is_liked']:
                reply_markup.append(
                    [InlineKeyboardButton(
                        text='unlike',
                        callback_data='unlike_' + str(products['id'])
                    )])
            else:
                reply_markup.append(
                    [InlineKeyboardButton(
                        text='like',
                        callback_data='like_' + str(products['id'])
                    )])
        buttons = pageButtons(links)
        reply_markup.append(buttons)
    else:
        text += 'Продукты не найдены'
    return text, reply_markup


async def productPager(u: Update, c: CallbackContext):
    params = f'&geo_id={c.user_data["geo_id"]['geo_id']}'
    data = linkData(u, c, params)
    token = c.user_data.get('token', '')
    if len(data) == 0:
        data = getProducts(c.user_data['geo_id'], token).json()
    print(data)
    text, reply_markup = productCardButtons(data, c)
    reply_markup.append(menuButton())
    await sendResolver(c, u, text=text, reply_markup=InlineKeyboardMarkup(reply_markup))


async def likedProductPager(u: Update, c: CallbackContext):
    token = c.user_data.get('token', '')
    data = linkData(u, c, hders= {"Accept": "application/json", "Authorization": f"Bearer {c.user_data['token']}"})
    if len(data) == 0:
        data = getLikedProducts(token).json()
        print(data)
    text, reply_markup = productCardButtons(data, c)
    reply_markup.append(menuButton())
    await sendResolver(c, u, text=text, reply_markup=InlineKeyboardMarkup(reply_markup))

async def showMenu(u: Update, c: CallbackContext):
    reply_markup = [
        [InlineKeyboardButton(
            text="Выбрать продукт",
            callback_data=CHOOSE_GEO
        )]
    ]
    if not c.user_data.get('token', ''):
        reply_markup.append([InlineKeyboardButton(
            text="Зарегистрироваться",
            callback_data=ENTER_NAME
        )])
    else:
        reply_markup.append([InlineKeyboardButton(
            text="Понравившиеся товары",
            callback_data=LIKED_PRODUCT_GEO_CARD
        )])
    await sendResolver(c, u, text="Выберите действие", reply_markup=InlineKeyboardMarkup(reply_markup))
    return ENTER_ACTION



async def handle_geo(u: Update, c: CallbackContext):
    data = u.callback_query.data
    if 'geo_id' in data:
        c.user_data['geo_id'] = {
            'geo_id': data.split('_')[-1]
        }
        c.user_data['link'] = ''
        await productPager(u, c)
        return PRODUCT_CARD
    if 'menu' in data:
        return await showMenu(u, c)
    else:
        c.user_data['link'] = data
        await geoPager(u, c)



async def handle_product(u: Update, c: CallbackContext):
    data = u.callback_query.data
    reply_markup = []
    if 'menu' in data:
        return await showMenu(u, c)
    if 'like' in data:
        product_id = data.split('_')[-1]
        print('ID:', product_id)
        response = likeProduct(product_id, c.user_data['token'])
        print(response)
        await u.callback_query.answer('Лайк поставлен')
        await productPager(u, c)
    else:
        c.user_data['link'] = data
        await productPager(u, c)


async def handle_liked_product_geo(u: Update, c: CallbackContext):
    data = u.callback_query.data
    if 'menu' in data:
        return await showMenu(u, c)
    if 'unlike' in data:
        product_id = data.split('_')[-1]
        print('ID:', product_id)
        response = unLikeProduct(product_id, c.user_data['token'])
        await u.callback_query.answer('Лайк поставлен')
        await likedProductPager(u, c)
        print(response)
    else:
        c.user_data['link'] = data
        await likedProductPager(u, c)

async def cmd_start(u: Update, c: CallbackContext):
    print(f"ADADADADADADAD: {c.user_data.get('PRIVET')}")
    if c.user_data.get('link'):
        del c.user_data['link']
    if c.user_data.get('messageId'):
        del c.user_data['messageId']
    return await showMenu(u, c)



async def handle_action(u: Update, c: CallbackContext):
    await u.callback_query.answer('Спасибо')
    action = int(u.callback_query.data)
    if action == CHOOSE_GEO:
        await geoPager(u, c)
    if action == ENTER_NAME:
        await sendResolver(c, u, text='Введите имя')
    if action == LIKED_PRODUCT_GEO_CARD:
        await likedProductPager(u, c)
    print(action)
    return action



async def handle_name(u: Update, c: CallbackContext):
    text = u.message.text
    c.user_data['name'] = text
    await sendResolver(c, u, text='Введите почту')
    return ENTER_EMAIL



async def handle_email(u: Update, c: CallbackContext):
    text = u.message.text
    c.user_data['email'] = text
    data = {
        'email': c.user_data['email'],
        'name': c.user_data['name'],
        'is_by_mail': 1,
        'telegram_id': u.message.from_user.id,
    }
    response = storeUser(data)
    try:
        response.raise_for_status()
    except httpx.HTTPError as e:
        if response.status_code == httpx.codes.UNPROCESSABLE_ENTITY:
            await u.effective_chat.send_message(f"{response.json()}")
            return ENTER_EMAIL
        else:
            await u.effective_chat.send_message(f"{response.json()}")
            return ENTER_EMAIL

    await sendResolver(c, u, text='Введите код')
    return ENTER_CODE



async def handle_code(u: Update, c: CallbackContext):
    text = u.message.text
    data = {
        'email': c.user_data['email'],
        'code': text
    }
    response = confirmUser(data).json()
    print(response)
    c.user_data['token'] = response['data']['token']
    return await showMenu(u, c)

async def sendResolver(c: CallbackContext, u: Update, **kwargs):
    messageId = c.user_data.get('messageId')
    bot = c.bot
    chat_id = u.effective_chat.id
    if messageId:
        if kwargs.get('text'):
            text = kwargs.get('text')
            if text and text != c.user_data['messageText'] or kwargs.get('reply_markup'):
                del kwargs['text']
                c.user_data['messageText'] = text
                await bot.edit_message_text(text, chat_id, messageId, **kwargs)
    else:
        message = await u.effective_chat.send_message(**kwargs)
        c.user_data['messageId'] = message.message_id
        c.user_data['messageText'] = message.text
        if not kwargs.get('reply_markup'):
            del c.user_data['messageId']
            del c.user_data['messageText']




def main():

    c_start = CommandHandler("start", cmd_start)


    main_conv_handler = ConversationHandler(
        entry_points=[c_start],
        states={
            ENTER_NAME: [
                c_start,
                MessageHandler(filters.TEXT, handle_name),
            ],
            ENTER_EMAIL: [c_start, MessageHandler(filters.TEXT, handle_email)],
            ENTER_CODE: [c_start, MessageHandler(filters.TEXT, handle_code)],
            ENTER_ACTION: [c_start, CallbackQueryHandler(handle_action)],
            CHOOSE_GEO: [c_start, CallbackQueryHandler(handle_geo)],
            PRODUCT_CARD: [c_start, CallbackQueryHandler(handle_product)],
            LIKED_PRODUCT_GEO_CARD: [c_start, CallbackQueryHandler(handle_liked_product_geo)]

        },
        fallbacks=[c_start],
        persistent=True,
        name="conv",
    )

    job_queue = app.job_queue

    job_queue.run_once(starter, 0)
    job_queue.run_repeating(consume_redis_messages, interval=2)

    app.add_handler(main_conv_handler)


    asyncio.run(app.run_polling(allowed_updates=Update.ALL_TYPES, timeout=180))

if __name__ == "__main__":
    main()