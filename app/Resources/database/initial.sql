INSERT INTO `saman_label` (`id`, `title`, `description`, `type`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`) VALUES
(1, 'A', '', NULL, NULL, NULL, 0, NULL),
(2, 'B', '', NULL, NULL, NULL, 0, NULL),
(3, 'C', '', NULL, NULL, NULL, 0, NULL),
(4, 'D', '', NULL, NULL, NULL, 0, NULL);


INSERT INTO `saman_role` (`id`, `name`, `role`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`) VALUES
(1, 'User', 'ROLE_USER', 0, 0, 0, NULL),
(2, 'Admin', 'ROLE_ADMIN', 0, 0, 0, NULL);


INSERT INTO `saman_users` (`id`, `username`, `password`, `email`, `is_active`, `salt`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`, `visibility`) VALUES
(1, 'admin@admin.com', '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.com', 1, '', 0, 0, 0, NULL, 0);


INSERT INTO `saman_users_roles` (`user_id`, `role_id`) VALUES
(1, 2);