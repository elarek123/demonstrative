from datetime import datetime

import redis.asyncio as redis
from telegram.ext import ApplicationBuilder, Application, PicklePersistence
from config import BOT_TOKEN
from config import REDIS_HOST, REDIS_PORT, REDIS_DB, REDIS_USERNAME, REDIS_PASSWORD

redis_client = redis.Redis(
    host=REDIS_HOST,
    port=REDIS_PORT,
    db=REDIS_DB,
    username=REDIS_USERNAME,
    password=REDIS_PASSWORD,
)

async def init_config(app: Application):
    await app.bot.set_my_commands(
        [
            ("start", "Запуск/перезапуск"),
        ]
    )

pickle_persistence = PicklePersistence('bot.data')

app = (
    ApplicationBuilder()
    .token(BOT_TOKEN)
    .post_init(init_config)
    .persistence(pickle_persistence)
    .concurrent_updates(True)
    .read_timeout(30)
    .write_timeout(30)
    .build()
)