/* ===== Users ===== */

/* password: password123 */
INSERT INTO users VALUES(
    'efc3fb4f-3b25-45b8-8a98-6c0f1db9ef3f', 
    'rafaelgirafa', 
    'baracri00@gmail.com', 
    '$2y$10$Z7Tmvja.hIJMyQRbdpPCyeclmzO3.gX8TgHGwXv8Cq.1dU7VD4UDq', 
    NULL, 
    0
);

/* password: password123 */
INSERT INTO users VALUES(
    'd862a769-47d7-4c9b-8690-b0e86584314a', 
    'fvelazq98', 
    'redscorpinox@gmail.com', 
    '$2y$10$Z7Tmvja.hIJMyQRbdpPCyeclmzO3.gX8TgHGwXv8Cq.1dU7VD4UDq', 
    NULL, 
    0
);

/* password: password123 */
INSERT INTO users VALUES(
    '7c7f0d05-022a-4c72-b728-767db671c329', 
    'crazy4u', 
    'other@other.com', 
    '$2y$10$Z7Tmvja.hIJMyQRbdpPCyeclmzO3.gX8TgHGwXv8Cq.1dU7VD4UDq', 
    NULL, 
    0
);

/* password: uGK.BGfcxhif-4kYiVMReMsN-dvLRK */
INSERT INTO users VALUES(
    'fff2c092-9bce-432f-b4db-39afe52dceb3',
    'Administrator',
    'admin@petlandia.com',
    '$2y$10$kNt9Y.YTKfD6Sd/LSav8Qu/5axNHs5cbt81Bh.k1wOX0.D2OeLgy.',
    NULL,
    1
);

/* ===== Posts ===== */

/* user: Rafael Barbieru */
INSERT INTO posts VALUES(
    '481f5cc1-4779-46b4-b402-e242308f897f',
    'Why is my bunny always peeing in the corners?',
    "My bunny always pees in the corners and I don't know how to make him stop. Help!",
    NULL,
    0,
    'efc3fb4f-3b25-45b8-8a98-6c0f1db9ef3f'
);
INSERT INTO posts VALUES(
    'a94d182c-7067-40ae-b16f-84e5dfc3c5bd',
    'A test',
    "Just a test",
    NULL,
    1,
    'efc3fb4f-3b25-45b8-8a98-6c0f1db9ef3f'
);

/* user: Fabio Velázquez */
INSERT INTO posts VALUES(
    '955a9f11-bf25-430e-a714-abdf1b46be7e',
    'I want a cat, should I adopt or buy?',
    "My mom tells me I should buy one because they come vaccinated, but everyone says I should adopt.",
    NULL,
    0,
    'd862a769-47d7-4c9b-8690-b0e86584314a'
);

/* ===== Comments ===== */

/* user: Tamara Arama, post: I want a cat */
INSERT INTO comments VALUES(
    '37ebc171-98da-4c80-83ea-57f58c7b38dc',
    '7c7f0d05-022a-4c72-b728-767db671c329',
    '955a9f11-bf25-430e-a714-abdf1b46be7e',
    'Always adopt!'
);