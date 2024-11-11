-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 10 2024 г., 22:44
-- Версия сервера: 10.4.28-MariaDB
-- Версия PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ads_database`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `additional_images` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `ads`
--

INSERT INTO `ads` (`id`, `user_id`, `title`, `description`, `price`, `main_image`, `additional_images`, `created_at`) VALUES
(1, 1, 'Продается уютная квартира в центре города', 'Продается 2-комнатная квартира площадью 60 кв. м. на 5 этаже. Вся мебель остается. Рядом парк и магазины.', 8500000.00, 'https://i.ibb.co/4VCC7T5/Ad-1-1.jpg', '[\"https://i.ibb.co/gd3ZMzz/ad-1-2.jpg\",\"https://i.ibb.co/1X6HtjN/Ad-1-3.jpg\"]', '2024-11-01 09:14:05'),
(2, 29, 'Авто в отличном состоянии', 'Продается Honda Civic 2017 года, пробег 50 000 км. Одна хозяйка, никогда не была в ДТП.', 950000.00, 'https://i.ibb.co/z8LZz8h/ad-2-1.jpg', NULL, '2024-11-30 09:14:05'),
(4, 1, 'Продам велосипеды по низким ценам', 'Продаются новые и б/у велосипеды разных моделей. Отличное состояние. Готовы к использованию.', 15000.00, 'https://i.ibb.co/88H9PNW/ad-4-1.jpg', 'https://i.ibb.co/xF8wJMR/ad-4-2.jpg&\nhttps://i.ibb.co/jy2nqjY/ad-4-3.jpg', '2024-11-01 09:14:05'),
(7, 1, 'Продажа ноутбука', 'Продается новый ноутбук, в отличном состоянии.', 50000.00, 'https://i.ibb.co/F4yGLsk/ad-7-1.jpg', '[\"image2.png\",\"image1.png\"]', '2024-11-01 17:54:06'),
(8, 1, 'Продается новый iPhone 14', 'Продается iPhone 14 в отличном состоянии, цвет: черный, память: 128 ГБ. Полная комплектация.', 65000.00, 'https://i.ibb.co/ygKyT3r/ad-8-1.jpg', NULL, '2024-11-01 22:29:49'),
(9, 1, 'Котенок ищет новый дом', 'Очаровательный котенок ищет заботливых хозяев. Привит и приучен к лотку.', 5000.00, 'https://i.ibb.co/jMkzXbz/ad-9-1.jpg', '[]', '2024-11-01 22:29:49'),
(10, 1, 'Гитара Fender Stratocaster', 'Продается гитара Fender Stratocaster в идеальном состоянии, с чехлом и струнами.', 30000.00, 'https://i.ibb.co/L5gQsj4/ad-10-1.jpg', '[]', '2024-11-01 22:29:49'),
(11, 1, 'Собака в добрые руки', 'Ищем хозяев для дружелюбной собаки. Привита, стерилизована, любит детей.', 0.00, 'https://i.ibb.co/HqbVGf5/ad-11-1.jpg', '[]', '2024-11-01 22:29:49'),
(12, 1, 'Уроки по игре на пианино', 'Индивидуальные уроки по игре на пианино. Уроки для детей и взрослых. Возможен выезд на дом.', 15000.00, 'https://i.ibb.co/sFK6f8g/ad-12-1.jpg', '[]', '2024-11-01 22:29:49'),
(13, 1, 'Мотоцикл Yamaha R1', 'Продается мотоцикл Yamaha R1 2020 года, пробег 5000 км, в идеальном состоянии.', 800000.00, 'https://i.ibb.co/3sYgpZD/ad-13-1.jpg', '[]', '2024-11-01 22:29:49'),
(14, 1, 'Электросамокат Xiaomi', 'Продается электросамокат Xiaomi, новый, максимальная скорость 25 км/ч, запас хода 30 км.', 25000.00, 'https://i.ibb.co/Xp7pLTp/ad-14-1.jpg', '[]', '2024-11-01 22:29:49'),
(15, 1, 'Кофемашина DeLonghi', 'Продается кофемашина DeLonghi, полностью автоматическая, в отличном состоянии.', 30000.00, 'https://i.ibb.co/yYmW6Fd/ad-15-1.jpg', '[]', '2024-11-01 22:29:49'),
(16, 1, 'Новая футбольная форма', 'Продается новая футбольная форма, размер M, цвет: красный. Подходит для детей и взрослых.', 4000.00, 'https://i.ibb.co/pQqTVpZ/ad-16-1.jpg', '[]', '2024-11-01 22:29:49'),
(17, 1, 'Горные лыжи и ботинки', 'Продается комплект горных лыж и ботинок, в отличном состоянии, длина лыж 170 см.', 15000.00, 'https://i.ibb.co/L905SXG/ad-17-1.jpg', '[]', '2024-11-01 22:29:49');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `is_admin`) VALUES
(1, 'maksim', 'maks@gmail.com', '123', '2024-11-01 16:06:27', 0),
(3, 'John Doe', 'johndoe@example.com', '$2y$10$tGyG27y6uuTQYnStcWQ.X.1Xlif6kVmga./QzhkW71DqpxCHl3MCO', '2024-11-07 18:41:22', 0),
(18, 'maksimj', 'maks@gmail.comj', '$2y$10$muHc6H/6Pl68r79LE3Hpbu9Zj3Z7BE7NaG/2LJQMUSVxZhJECLZNa', '2024-11-07 18:52:23', 0),
(26, 'maksimkaaafdsfds', 'maks@gmail.comdsfds', '$2y$10$u3MTGhtudwJVO5Ny039xwO/0U3NHPE0.o4tOxZtB3YAtv.FvV.KQi', '2024-11-07 19:19:16', 0),
(27, 'maksimkaaafdв', 'makskaa@gmail.comfd', '$2y$10$4Urmj7wio0iRo78G9lecMebz178Qe0p4hJi5EwogQEwjxZ0YBjPhe', '2024-11-07 19:19:43', 0),
(28, 'maksimkaaafdsdf', 'makskaa@gmail.comfdsdsf', '$2y$10$0bx7A8q1y2jVfu5k.Ch6ROz9AxssPYKZDSRtYWlbdp9cLg4A22oL.', '2024-11-07 19:20:00', 0),
(29, 'maksimka', 'maksimka@gmail.com', '$2y$10$0sCHHpbW1DOxcDimv8pDUOWnH9w2K4DF73wjiViPvscErsJrRqxPO', '2024-11-07 19:29:12', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
