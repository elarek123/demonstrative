import asyncio
import json

from telegram import (
    Update,
    InlineKeyboardButton,
    InlineKeyboardMarkup, Message,
)
from telegram.constants import ParseMode

from telegram.ext import ContextTypes


from loader import app


async def sender(tg_id, json_data, context_user_data):
    bot = app.bot
    message = await bot.send_message(
        chat_id=tg_id,
        text=f"Цена на продукт {json_data['product_name']} из {json_data['geo_name']} изменена на {json_data['price']}",
        parse_mode=ParseMode.HTML,
    )
    if app.user_data.get(tg_id):
        if app.user_data[tg_id].get('messageId'):
            del app.user_data[tg_id]['messageId']
    print(f"LOLOLOLOLOLOLOLOLOOLOL: {app.user_data[tg_id]['data']}")

async def reader(message, context: ContextTypes.DEFAULT_TYPE) -> None:
    if not message:
        return
    message_data = message['data']

    tasks = []
    json_data = json.loads(message_data)

    context_user_data = await app.persistence.get_user_data()

    print(json_data)


    for telegram_id in json_data['telegram_ids']:
        tg_id = int(telegram_id)
        tasks.append(
            sender(tg_id, json_data, context_user_data)
        )


    await asyncio.gather(*tasks)
    await asyncio.sleep(1)