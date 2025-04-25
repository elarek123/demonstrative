from dotenv import dotenv_values


ENV = dotenv_values(".env")

BOT_TOKEN = ENV['BOT_TOKEN']

API_URL = ENV.get("API_URL", 'localhost:8080/api')

REDIS_HOST = ENV.get('REDIS_HOST', '127.0.0.1')
REDIS_PORT = ENV.get('REDIS_PORT', 6379)
REDIS_DB = ENV.get('REDIS_DB', 0)
REDIS_USERNAME = ENV.get('REDIS_USERNAME', None)
REDIS_PASSWORD = ENV.get('REDIS_PASSWORD', None)


REDIS_CHANNEL = ENV.get('REDIS_CHANNEL')
