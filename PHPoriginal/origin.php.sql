-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:3305
-- 生成日時: 2021 年 9 月 05 日 01:41
-- サーバのバージョン： 5.7.32
-- PHP のバージョン: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- データベース: `origin_php`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`id`, `category`) VALUES
(1, '禁煙開始宣言'),
(2, '禁煙初日〜１週間'),
(3, '禁煙１週間〜1ヶ月'),
(4, '禁煙1ヶ月〜3ヶ月'),
(5, '禁煙3ヶ月〜1年');

-- --------------------------------------------------------

--
-- テーブルの構造 `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trouble_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `comment` varchar(256) NOT NULL,
  `create_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='悩み内容へのコメント情報';

--
-- テーブルのデータのダンプ `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `trouble_id`, `name`, `comment`, `create_at`) VALUES
(4, 4, 5, '土方', '頑張りましょう！', '2021-09-01 11:08:48'),
(5, 7, 5, 'test4', 'a', '2021-09-04 23:59:15'),
(6, 7, 5, 'a', 'a', '2021-09-05 00:04:19');

-- --------------------------------------------------------

--
-- テーブルの構造 `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `trouble_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `favorites`
--

INSERT INTO `favorites` (`user_id`, `trouble_id`) VALUES
(4, 6);

-- --------------------------------------------------------

--
-- テーブルの構造 `troubles`
--

CREATE TABLE `troubles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `trouble` varchar(256) NOT NULL,
  `create_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `troubles`
--

INSERT INTO `troubles` (`id`, `user_id`, `category_id`, `name`, `trouble`, `create_at`) VALUES
(1, 2, 1, 'sanji', '今日から禁煙始めます。', '2021-03-31 18:53:12'),
(2, 2, 2, 'sanji', 'まだ余裕', '2021-04-04 18:54:38'),
(3, 2, 3, 'sanji', '気を引き締めろ', '2021-04-30 18:55:37'),
(4, 2, 4, 'sanji', 'タバコより大切なものに気がつきました', '2021-05-12 18:56:40'),
(5, 2, 5, 'sanji', 'もう全然吸いたいと思いません！\r\n禁煙できました！', '2022-08-31 18:58:12'),
(6, 1, 1, 'カイジです', 'ぁぁぁぁぁぁあああああああああ', '2021-08-31 22:26:39'),
(9, 4, 1, '土方', '禁煙始めます', '2021-09-01 00:57:12'),
(10, 5, 1, '高井', '今日から頑張ります！', '2021-09-02 20:09:04'),
(11, 7, 1, 'test4', 'hhh', '2021-09-04 23:57:21'),
(13, 8, 1, 'test4', 'oh', '2021-09-03 00:36:41');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `kana` varchar(32) NOT NULL,
  `number` int(11) NOT NULL COMMENT '本数',
  `mail` varchar(256) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(1) DEFAULT '1' COMMENT '0=管理者、1=一般ユーザー',
  `delflag` int(11) NOT NULL DEFAULT '1' COMMENT '0=削除済,1=デフォルト'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ユーザー情報';

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `name`, `kana`, `number`, `mail`, `password`, `role`, `delflag`) VALUES
(1, '伊藤開司', 'いとうかいじ', 10, 'akitotakai14@gmail.com', '$2y$10$humCAzFFW239qOMjSQZDwOsYz1PjjG72Z4gQQ6tdqxSORoDoPc8Zq', 1, 1),
(2, 'ヴィンスモーク・サンジ', 'ヴィンスモーク・サンジ', 20, 'test@icloud.com', '$2y$10$2K2YX4N3BsmIgHo6qTVxiucbLilk1aJjyNr/JFRwAtj1KMJmGnKvy', 1, 1),
(3, 'デューク・東郷', 'デューク・トウゴウ', 25, 'takai1414@icloud.com', '$2y$10$d1K59vIgcL.1xGV74zVs4.Ojo1KagyHi46T4Ty8aUqq4PB/WuLCei', 0, 1),
(4, '土方十四郎', 'ヒジカタトウシロウ', 14, 'test2@gmail.com', '$2y$10$YqWq3htD8mwBP1dMUQ9S4uGE2jROPrN2DtW9CvTX35UGZLLqAMo/.', 1, 1),
(5, '高井彰人', 'タカイアキト', 8, 'test3@gmail.com', '$2y$10$FqIAl81wO0grOTwPAQkM7.M4DdUeNZFvVckCXZL35ZydK2iXjs.ba', 1, 1),
(8, 'test4', 'test4', 10, 'test4@gmail.com', '$2y$10$0gO3M0cXARFhylIkk3D8HeFevv.Dq6ohct/ujkvqFCBaiNkqtTBPy', 1, 1);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `troubles`
--
ALTER TABLE `troubles`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `troubles`
--
ALTER TABLE `troubles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
