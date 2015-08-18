INSERT INTO `saman_label` (`id`, `title`, `description`, `type`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`) VALUES
(1, 'A', '', NULL, NULL, NULL, 0, NULL),
(2, 'B', '', NULL, NULL, NULL, 0, NULL),
(3, 'C', '', NULL, NULL, NULL, 0, NULL),
(4, 'D', '', NULL, NULL, NULL, 0, NULL);


INSERT INTO `saman_role` (`id`, `name`, `role`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`) VALUES
(1, 'User', 'ROLE_USER', 0, 0, 0, NULL),
(2, 'Admin', 'ROLE_ADMIN', 0, 0, 0, NULL);


INSERT INTO `saman_users` (`id`, `username`, `password`, `email`, `is_active`, `salt`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`) VALUES
(1, 'saman@gmail.com', '7eb681a31a3c08ee6a429a81fc97e677', 'saman@gmail.com', 1, '', 0, 0, 0, NULL);


INSERT INTO `saman_users_roles` (`user_id`, `role_id`) VALUES
(1, 2);