# Telegram Bot + Laravel API: Гео-ориентированный каталог товаров

## О проекте
Система предоставляет:
- Регистрацию/авторизацию через Telegram бота
- Выбор географических регионов (Geo)
- Персонализированную ленту товаров на основе выбранных гео
- Возможность лайкать/дизлайкать товары
- Push-уведомления об изменении цены понравившихся товаров

## Технологический стек
- **Backend**: Laravel
- **Библиотека для работы с ботом**: python-telegram-bot
- **Push-уведомления**: Redis

## Функционал API

###  Авторизация
- Регистрация через Telegram (`/auth/telegram`)
- Подтверждение пользователя (`/auth/confirmation`)
