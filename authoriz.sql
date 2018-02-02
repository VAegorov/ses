-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Фев 02 2018 г., 21:10
-- Версия сервера: 10.1.21-MariaDB
-- Версия PHP: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authoriz`
--

CREATE TABLE `authoriz` (
  `id` int(5) NOT NULL,
  `name` varchar(64) NOT NULL,
  `surname` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `login` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  `city` varchar(64) NOT NULL,
  `age` date NOT NULL,
  `language` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `authoriz`
--

INSERT INTO `authoriz` (`id`, `name`, `surname`, `email`, `login`, `password`, `date`, `city`, `age`, `language`) VALUES
(1, 'василий', 'егоров', 'kot2046@gmail.com', 'kot2046', 'qweasd', '2018-02-02 01:13:00', 'Псков', '1974-06-12', 'русский'),
(2, 'юрий', 'петров', 'kaka$gmail.com', 'yri', 'qweasd', '0000-00-00 00:00:00', 'псков', '1975-12-15', 'русский'),
(3, 'Дмитрий', 'Васильев', 'frah@gmail.com', 'frah', '123', '2018-02-02 11:19:03', 'псков', '1974-06-10', 'русский'),
(4, 'fdds', 'sdfg', 'sdfg', 'fgr', 'ac6475ae', '2018-02-02 12:16:58', 'sdfg', '2018-02-20', 'sdfg');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authoriz`
--
ALTER TABLE `authoriz`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authoriz`
--
ALTER TABLE `authoriz`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
