insert into skill values (1, 'Coordinate System', 'coordinate-system', now(), now());
insert into tutorial_skill values (1, 1, now());

insert into skill values (2, 'Drawing Basic Shapes', 'drawing-basic-shapes', now(), now());
insert into tutorial_skill values (1, 2, now());

insert into skill values (3, 'Colours', 'colours', now(), now());
insert into tutorial_skill values (1, 3, now());

insert into skill values (4, 'Comments and Errors', 'comments-and-errors', now(), now());
insert into tutorial_skill values (1, 4, now());

-- Question 1
insert into skill_question VALUES (1, 1, 1, 'What is the width of the canvas when the following command is run?<pre>setCanvasSize(640, 360)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (1, 1, 'setCanvasSize does not set the width of the canvas', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (2, 1, '1000', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (3, 1, '360', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (4, 1, '640', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (1, 1, 'The setCanvasSize() function sets the width and the height of the canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (2, 1, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (3, 1, 'As 640 is the first parameter, this is the width the canvas will be set to.', NULL, 3, now(), now());

-- Question 2
insert into skill_question VALUES (2, 1, 1, 'What is the height of the canvas when the following command is run?<pre>setCanvasSize(640, 360)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (5, 2, 'setCanvasSize does not set the height of the canvas', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (6, 2, '1000', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (7, 2, '360', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (8, 2, '640', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (4, 2, 'The setCanvasSize() function sets the width and the height of the canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (5, 2, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (6, 2, 'As 360 is the second parameter, this is the height the canvas will be set to.', NULL, 3, now(), now());

-- Question 3
insert into skill_question VALUES (3, 1, 1, 'What is the width of the canvas when the following command is run?<pre>setCanvasSize(1000, 300)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (9, 3, '1000', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (10, 3, '300', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (11, 3, '1300', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (12, 3, 'setCanvasSize does not set the width of the canvas', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (7, 3, 'The setCanvasSize() function sets the width and the height of the canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (8, 3, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (9, 3, 'As 1000 is the first parameter, this is the width the canvas will be set to.', NULL, 3, now(), now());

-- Question 4
insert into skill_question VALUES (4, 1, 1, 'What is the height of the canvas when the following command is run?<pre>setCanvasSize(1000, 300)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (13, 4, '1000', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (14, 4, '300', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (15, 4, '1300', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (16, 4, 'setCanvasSize does not set the height of the canvas', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (10, 4, 'The setCanvasSize() function sets the width and the height of the canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (11, 4, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (12, 4, 'As 300 is the second parameter, this is the height the canvas will be set to.', NULL, 3, now(), now());

-- Question 5
insert into skill_question VALUES (5, 1, 1, 'Where is the bottom-left corner of the canvas after the following command is run?<pre>setCanvasSize(640, 360)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (17, 5, '(0, 0)', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (18, 5, '(639, 0)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (19, 5, '(0, 359)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (20, 5, '(639, 359)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (13, 5, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (14, 5, 'The coordinates for the width and height both start at zero.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (15, 5, 'The bottom-left corner is at (0, 0).', NULL, 3, now(), now());

-- Question 6
insert into skill_question VALUES (6, 1, 1, 'Where is the bottom-right corner of the canvas after the following command is run?<pre>setCanvasSize(640, 360)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (21, 6, '(0, 0)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (22, 6, '(639, 0)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (23, 6, '(0, 359)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (24, 6, '(639, 359)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (16, 6, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (17, 6, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (18, 6, 'The bottom-right corner is at (639, 0).', NULL, 3, now(), now());

-- Question 7
insert into skill_question VALUES (7, 1, 1, 'Where is the top-left corner of the canvas after the following command is run?<pre>setCanvasSize(640, 360)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (25, 7, '(0, 0)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (26, 7, '(639, 0)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (27, 7, '(0, 359)', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (28, 7, '(639, 359)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (19, 7, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (20, 7, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (21, 7, 'The top-left corner is at (0, 359).', NULL, 3, now(), now());

-- Question 8
insert into skill_question VALUES (8, 1, 1, 'Where is the top-right corner of the canvas after the following command is run?<pre>setCanvasSize(640, 360)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (29, 8, '(0, 0)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (30, 8, '(639, 0)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (31, 8, '(0, 359)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (32, 8, '(639, 359)', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (22, 8, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (23, 8, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (24, 8, 'The top-right corner is at (639, 359).', NULL, 3, now(), now());

-- Question 9
insert into skill_question VALUES (9, 1, 1, 'Where is the bottom left corner of the canvas after the following command is run?<pre>setCanvasSize(200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (33, 9, '(1, 1)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (34, 9, '(0, 0)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (35, 9, '(0, 359)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (36, 9, '(0, 360)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (25, 9, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (26, 9, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (27, 9, 'The bottom-left corner is at (0, 0).', NULL, 3, now(), now());

-- Question 10
insert into skill_question VALUES (10, 1, 1, 'Where is the bottom right corner of the canvas after the following command is run?<pre>setCanvasSize(200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (37, 10, '(0, 199)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (38, 10, '(199, 1)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (39, 10, '(199, 0)', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (40, 10, '(200, 0)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (28, 10, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (29, 10, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (30, 10, 'The bottom-right corner is at (199, 0).', NULL, 3, now(), now());

-- Question 11
insert into skill_question VALUES (11, 1, 1, 'Where is the top right corner of the canvas after the following command is run?<pre>setCanvasSize(200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (41, 11, '(200, 100)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (42, 11, '(199, 99)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (43, 11, '(0, 100)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (44, 11, '(0, 99)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (31, 11, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (32, 11, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (33, 11, 'The top-right corner is at (199, 99).', NULL, 3, now(), now());

-- Question 12
insert into skill_question VALUES (12, 1, 1, 'Where is the top left corner of the canvas after the following command is run?<pre>setCanvasSize(200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (45, 12, '(0, 100)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (46, 12, '(0, 99)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (47, 12, '(1, 100)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (48, 12, '(1, 99)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (34, 12, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (35, 12, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (36, 12, 'The top-left corner is at (0, 99).', NULL, 3, now(), now());

-- Question 13
insert into skill_question VALUES (13, 1, 1, 'Where is the bottom right corner of the canvas after the following command is run?<pre>setCanvasSize(1000, 300)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (49, 13, '(999, 0)', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (50, 13, '(999, 1)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (51, 13, '(0, 999)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (52, 13, '(1, 999)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (37, 13, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (38, 13, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (39, 13, 'The bottom-right corner is at (999, 0).', NULL, 3, now(), now());

-- Question 14
insert into skill_question VALUES (14, 1, 1, 'Where is the top right corner of the canvas after the following command is run?<pre>setCanvasSize(1000, 300)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (53, 14, '(999, 0)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (54, 14, '(1000, 300)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (55, 14, '(999, 299)', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (56, 14, '(0, 0)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (40, 14, 'The first parameter to the setCanvasSize() function is the width and the second is the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (41, 14, 'The coordinates for the width and height both start at zero and go to one less than the specified parameter.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (42, 14, 'The top-right corner is at (999, 299).', NULL, 3, now(), now());

-- Question 15
insert into skill_question VALUES (15, 1, 1, 'What are the coordinates of the following point?', 'coordinate-system-01.png', now(), now());
insert into skill_question_option VALUES (57, 15, '(100, 60)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (58, 15, '(160, 0)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (59, 15, '(0, 160)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (60, 15, '(60, 100)', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (43, 15, 'The default coordinate system in PyAngelo is similar to the cartesian plane learned in mathematics.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (44, 15, 'To specify a point you list the x coordinate first, followed by the y coordinate.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (45, 15, 'The illustrated point is located at (60, 100).', NULL, 3, now(), now());

-- Question 16
insert into skill_question VALUES (16, 1, 1, 'What are the coordinates of the following point?', 'coordinate-system-02.png', now(), now());
insert into skill_question_option VALUES (61, 16, '(20, 160)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (62, 16, '(160, 160)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (63, 16, '(20, 140)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (64, 16, '(140, 20)', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (46, 16, 'The default coordinate system in PyAngelo is similar to the cartesian plane learned in mathematics.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (47, 16, 'To specify a point you list the x coordinate first, followed by the y coordinate.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (48, 16, 'The illustrated point is located at (140, 20).', NULL, 3, now(), now());

-- Question 17
insert into skill_question VALUES (17, 1, 1, 'What are the coordinates of the following point?', 'coordinate-system-03.png', now(), now());
insert into skill_question_option VALUES (65, 17, '(180, 60)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (66, 17, '(60, 180)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (67, 17, '(240, 240)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (68, 17, '(180, 0)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (49, 17, 'The default coordinate system in PyAngelo is similar to the cartesian plane learned in mathematics.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (50, 17, 'To specify a point you list the x coordinate first, followed by the y coordinate.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (51, 17, 'The illustrated point is located at (60, 180).', NULL, 3, now(), now());

-- Question 18
insert into skill_question VALUES (18, 1, 1, 'What are the coordinates of the following point?', 'coordinate-system-04.png', now(), now());
insert into skill_question_option VALUES (69, 18, '(40, 20)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (70, 18, '(60, 60)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (71, 18, '(20, 40)', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (72, 18, '(20, 60)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (52, 18, 'The default coordinate system in PyAngelo is similar to the cartesian plane learned in mathematics.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (53, 18, 'To specify a point you list the x coordinate first, followed by the y coordinate.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (54, 18, 'The illustrated point is located at (20, 40).', NULL, 3, now(), now());

-- Question 19
insert into skill_question VALUES (19, 1, 1, 'What are the coordinates of the following point?', 'coordinate-system-05.png', now(), now());
insert into skill_question_option VALUES (73, 19, '(120, 100)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (74, 19, '(100, 120)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (75, 19, '(220, 100)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (76, 19, '(220, 220)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (55, 19, 'The default coordinate system in PyAngelo is similar to the cartesian plane learned in mathematics.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (56, 19, 'To specify a point you list the x coordinate first, followed by the y coordinate.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (57, 19, 'The illustrated point is located at (100, 120).', NULL, 3, now(), now());

-- Question 20
insert into skill_question VALUES (20, 1, 1, 'What are the coordinates of the following point?', 'coordinate-system-06.png', now(), now());
insert into skill_question_option VALUES (77, 20, '(100, 40)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (78, 20, '(60, 40)', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (79, 20, '(40, 60)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (80, 20, '(100, 100)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (58, 20, 'The default coordinate system in PyAngelo is similar to the cartesian plane learned in mathematics.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (59, 20, 'To specify a point you list the x coordinate first, followed by the y coordinate.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (60, 20, 'The illustrated point is located at (60, 40).', NULL, 3, now(), now());

-- Question 21
insert into skill_question VALUES (21, 2, 1, 'What is the height of a rectangle drawn with the following code?<pre>rect(10, 20, 30, 40)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (81, 21, '10', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (82, 21, '20', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (83, 21, '30', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (84, 21, '40', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (61, 21, 'To draw a rectangle in PyAngelo you specify the bottom left corner of the rectangle, the width and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (62, 21, 'The four arguments to the rect() function in order are the x location, y location, width and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (63, 21, 'The height specified in this example is 40.', NULL, 3, now(), now());

-- Question 22
insert into skill_question VALUES (22, 2, 1, 'What is the width of a rectangle drawn with the following code?<pre>rect(10, 20, 30, 40)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (85, 22, '10', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (86, 22, '20', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (87, 22, '30', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (88, 22, '40', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (64, 22, 'To draw a rectangle in PyAngelo you specify the bottom left corner of the rectangle, the width and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (65, 22, 'The four arguments to the rect() function in order are the x location, y location, width and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (66, 22, 'The width specified in this example is 30.', NULL, 3, now(), now());

-- Question 23
insert into skill_question VALUES (23, 2, 1, 'What is the location of the bottom-left corner of a rectangle drawn with the following code?<pre>rect(10, 20, 30, 40)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (89, 23, '(10, 20)', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (90, 23, '(20, 30)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (91, 23, '(30, 40)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (92, 23, '(10, 40)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (67, 23, 'To draw a rectangle in PyAngelo you specify the bottom left corner of the rectangle, the width and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (68, 23, 'The four arguments to the rect() function in order are the x location, y location, width and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (69, 23, 'The bottom-left corner specified in this example is (10, 20).', NULL, 3, now(), now());

-- Question 24
insert into skill_question VALUES (24, 2, 1, 'What is the radius of a circle drawn with the following code?<pre>circle(320, 180, 50)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (93, 24, '320', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (94, 24, '180', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (95, 24, '50', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (96, 24, '25', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (70, 24, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (71, 24, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (72, 24, 'The radius of the circle specified in this example is 50.', NULL, 3, now(), now());

-- Question 25
insert into skill_question VALUES (25, 2, 1, 'What is the location of the centre of the circle drawn with the following code?<pre>circle(320, 180, 50)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (97, 25, '(180, 50)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (98, 25, '(50, 180)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (99, 25, '(320, 180)', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (100, 25, '(180, 320)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (73, 25, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (74, 25, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (75, 25, 'The centre of the circle specified in this example is (320, 180).', NULL, 3, now(), now());

-- Question 26
insert into skill_question VALUES (26, 2, 1, 'What is the height of a rectangle drawn with the following code?<pre>rect(320, 180, 200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (101, 26, '100', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (102, 26, '200', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (103, 26, '180', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (104, 26, '320', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (76, 26, 'To draw a rectangle in PyAngelo you specify the bottom left corner of the rectangle, the width and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (77, 26, 'The four arguments to the rect() function in order are the x location, y location, width and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (78, 26, 'The height of the rectangle specified in this example is 100.', NULL, 3, now(), now());

-- Question 27
insert into skill_question VALUES (27, 2, 1, 'What is the width of a rectangle drawn with the following code?<pre>rect(320, 180, 200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (105, 27, '100', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (106, 27, '200', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (107, 27, '180', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (108, 27, '320', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (79, 27, 'To draw a rectangle in PyAngelo you specify the bottom left corner of the rectangle, the width and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (80, 27, 'The four arguments to the rect() function in order are the x location, y location, width and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (81, 27, 'The width of the rectangle specified in this example is 200.', NULL, 3, now(), now());

-- Question 28
insert into skill_question VALUES (28, 2, 1, 'What is the location of the bottom-left corner of a rectangle drawn with the following code?<pre>rect(320, 180, 200, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (109, 28, '(200, 100)', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (110, 28, '(180, 200)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (111, 28, '(320, 180)', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (112, 28, '(320, 100)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (82, 28, 'To draw a rectangle in PyAngelo you specify the bottom left corner of the rectangle, the width and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (83, 28, 'The four arguments to the rect() function in order are the x location, y location, width and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (84, 28, 'The bottom-left corner of the rectangle specified in this example is (320, 180).', NULL, 3, now(), now());

-- Question 29
insert into skill_question VALUES (29, 2, 1, 'What is the radius of a circle drawn with the following code?<pre>circle(75, 125, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (113, 29, '50', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (114, 29, '100', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (115, 29, '125', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (116, 29, '75', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (85, 29, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (86, 29, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (87, 29, 'The radius of the circle specified in this example is 100.', NULL, 3, now(), now());

-- Question 30
insert into skill_question VALUES (30, 2, 1, 'What is the location of the centre of the circle drawn with the following code?<pre>circle(75, 125, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (117, 30, '(75, 125)', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (118, 30, '(75, 100)', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (119, 30, '(125, 100)', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (120, 30, '(125, 75)', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (88, 30, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (89, 30, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (90, 30, 'The centre of the circle specified in this example is (75, 125).', NULL, 3, now(), now());

-- Question 31
insert into skill_question VALUES (31, 2, 1, 'What does the number 55 represent in the following line of code?<pre>rect(55, 65, 50, 60)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (121, 31, 'The x location of the bottom-left corner of the rectangle', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (122, 31, 'The y location of the bottom-left corner of the rectangle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (123, 31, 'The width of the rectangle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (124, 31, 'The height of the rectangle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (91, 31, 'To draw a rectangle in PyAngelo you specify the bottom-left corner of the rectangle, the width, and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (92, 31, 'The four arguments to the rect() function in order are the x location, y location, width, and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (93, 31, 'The number 55 in this example represents the x location of the bottom-left corner of the rectangle.', NULL, 3, now(), now());

-- Question 32
insert into skill_question VALUES (32, 2, 1, 'What does the number 65 represent in the following line of code?<pre>rect(55, 65, 50, 60)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (125, 32, 'The x location of the bottom-left corner of the rectangle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (126, 32, 'The y location of the bottom-left corner of the rectangle', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (127, 32, 'The width of the rectangle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (128, 32, 'The height of the rectangle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (94, 32, 'To draw a rectangle in PyAngelo you specify the bottom-left corner of the rectangle, the width, and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (95, 32, 'The four arguments to the rect() function in order are the x location, y location, width, and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (96, 32, 'The number 65 in this example represents the y location of the bottom-left corner of the rectangle.', NULL, 3, now(), now());

-- Question 33
insert into skill_question VALUES (33, 2, 1, 'What does the number 50 represent in the following line of code?<pre>rect(55, 65, 50, 60)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (129, 33, 'The x location of the bottom-left corner of the rectangle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (130, 33, 'The y location of the bottom-left corner of the rectangle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (131, 33, 'The width of the rectangle', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (132, 33, 'The height of the rectangle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (97, 33, 'To draw a rectangle in PyAngelo you specify the bottom-left corner of the rectangle, the width, and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (98, 33, 'The four arguments to the rect() function in order are the x location, y location, width, and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (99, 33, 'The number 50 in this example represents the width of the rectangle.', NULL, 3, now(), now());

-- Question 34
insert into skill_question VALUES (34, 2, 1, 'What does the number 60 represent in the following line of code?<pre>rect(55, 65, 50, 60)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (133, 34, 'The x location of the bottom-left corner of the rectangle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (134, 34, 'The y location of the bottom-left corner of the rectangle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (135, 34, 'The width of the rectangle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (136, 34, 'The height of the rectangle', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (100, 34, 'To draw a rectangle in PyAngelo you specify the bottom-left corner of the rectangle, the width, and the height.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (101, 34, 'The four arguments to the rect() function in order are the x location, y location, width, and height', NULL, 2, now(), now());
insert into skill_question_hint VALUES (102, 34, 'The number 60 in this example represents the height of the rectangle.', NULL, 3, now(), now());

-- Question 35
insert into skill_question VALUES (35, 2, 1, 'What does the number 125 represent in the following line of code?<pre>circle(125, 175, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (137, 35, 'The x location of the centre of the circle', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (138, 35, 'The y location of the centre of the circle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (139, 35, 'The radius of the circle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (140, 35, 'The diameter of the circle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (103, 35, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (104, 35, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (105, 35, 'The number 125 in this example represents the x location of the centre of the circle.', NULL, 3, now(), now());

-- Question 36
insert into skill_question VALUES (36, 2, 1, 'What does the number 175 represent in the following line of code?<pre>circle(125, 175, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (141, 36, 'The x location of the centre of the circle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (142, 36, 'The y location of the centre of the circle', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (143, 36, 'The radius of the circle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (144, 36, 'The diameter of the circle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (106, 36, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (107, 36, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (108, 36, 'The number 175 in this example represents the y location of the centre of the circle.', NULL, 3, now(), now());

-- Question 37
insert into skill_question VALUES (37, 2, 1, 'What does the number 100 represent in the following line of code?<pre>circle(125, 175, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (145, 37, 'The x location of the centre of the circle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (146, 37, 'The y location of the centre of the circle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (147, 37, 'The radius of the circle', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (148, 37, 'The diameter of the circle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (109, 37, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (110, 37, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (111, 37, 'The number 100 in this example represents the radius of the circle.', NULL, 3, now(), now());

-- Question 38
insert into skill_question VALUES (38, 2, 1, 'What does the number 50 represent in the following line of code?<pre>circle(50, 25, 20)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (149, 38, 'The radius of the circle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (150, 38, 'The diameter of the circle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (151, 38, 'The x location of the centre of the circle', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (152, 38, 'The y location of the centre of the circle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (112, 38, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (113, 38, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (114, 38, 'The number 50 in this example represents the x location of the centre of the circle.', NULL, 3, now(), now());

-- Question 39
insert into skill_question VALUES (39, 2, 1, 'What does the number 25 represent in the following line of code?<pre>circle(50, 25, 20)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (153, 39, 'The radius of the circle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (154, 39, 'The diameter of the circle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (155, 39, 'The x location of the centre of the circle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (156, 39, 'The y location of the centre of the circle', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (115, 39, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (116, 39, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (117, 39, 'The number 25 in this example represents the y location of the centre of the circle.', NULL, 3, now(), now());

-- Question 40
insert into skill_question VALUES (40, 2, 1, 'What does the number 20 represent in the following line of code?<pre>circle(50, 25, 20)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (157, 40, 'The radius of the circle', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (158, 40, 'The diameter of the circle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (159, 40, 'The x location of the centre of the circle', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (160, 40, 'The y location of the centre of the circle', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (118, 40, 'To draw a circle in PyAngelo you specify the centre of the circle and the radius.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (119, 40, 'The three arguments to the circle() function in order are the x location, y location, and radius', NULL, 2, now(), now());
insert into skill_question_hint VALUES (120, 40, 'The number 20 in this example represents the radius of the circle.', NULL, 3, now(), now());

-- Question 41
insert into skill_question VALUES (41, 3, 1, 'What does RGB stand for?', NULL, now(), now());
insert into skill_question_option VALUES (161, 41, 'Really Good Balance', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (162, 41, 'Red Green Blue', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (163, 41, 'Red Gold Black ', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (164, 41, 'Radiant Gold Blanket', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (121, 41, 'PyAngelo uses the RGB colour scheme.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (122, 41, 'RGB is an additive colour model in which colours of light are added together.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (123, 41, 'The RGB colour model specifies the amount of red, green, and blue to combine.', NULL, 3, now(), now());

-- Question 42
insert into skill_question VALUES (42, 3, 1, 'What colour will the background be after the following command is executed?<pre>background(255, 0, 0)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (165, 42, 'Blue', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (166, 42, 'Green', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (167, 42, 'Red', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (168, 42, 'Black', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (124, 42, 'The background() function draws a rectangle of a certain colour over the entire canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (125, 42, 'The colour of the background is specified by the first 3 arguments.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (126, 42, 'The arguments in order are red, green, blue.', NULL, 3, now(), now());

-- Question 43
insert into skill_question VALUES (43, 3, 1, 'What colour will the background be after the following command is executed?<pre>background(0, 255, 0)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (169, 43, 'Red', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (170, 43, 'Green', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (171, 43, 'Blue', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (172, 43, 'White', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (127, 43, 'The background() function draws a rectangle of a certain colour over the entire canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (128, 43, 'The colour of the background is specified by the first 3 arguments.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (129, 43, 'The arguments in order are red, green, blue.', NULL, 3, now(), now());

-- Question 44
insert into skill_question VALUES (44, 3, 1, 'What colour will the background be after the following command is executed?<pre>background(0, 0, 255)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (173, 44, 'Red', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (174, 44, 'Green', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (175, 44, 'Blue', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (176, 44, 'Pink', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (130, 44, 'The background() function draws a rectangle of a certain colour over the entire canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (131, 44, 'The colour of the background is specified by the first 3 arguments.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (132, 44, 'The arguments in order are red, green, blue.', NULL, 3, now(), now());

-- Question 45
insert into skill_question VALUES (45, 3, 1, 'What colour will the background be after the following command is executed?<pre>background(0, 0, 0)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (177, 45, 'White', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (178, 45, 'Black', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (179, 45, 'Blue', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (180, 45, 'Green', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (133, 45, 'The background() function draws a rectangle of a certain colour over the entire canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (134, 45, 'The arguments in order are red, green, blue.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (135, 45, 'If all colours are at zero intensity, the colour will be black.', NULL, 3, now(), now());

-- Question 46
insert into skill_question VALUES (46, 3, 1, 'What colour will the background be after the following command is executed?<pre>background(255, 255, 255)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (181, 46, 'Green', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (182, 46, 'Blue', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (183, 46, 'Black', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (184, 46, 'White', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (136, 46, 'The background() function draws a rectangle of a certain colour over the entire canvas.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (137, 46, 'The arguments in order are red, green, blue.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (138, 46, 'If all colours are at full intensity, the colour will be white.', NULL, 3, now(), now());

-- Question 47
insert into skill_question VALUES (47, 3, 1, 'What does the fill(r, g, b) function do?', NULL, now(), now());
insert into skill_question_option VALUES (185, 47, 'It sets the background of the canvas to the specified colour.', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (186, 47, 'It sets the colour of the border for any subsequent shapes that are drawn.', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (187, 47, 'There is no fill() function in PyAngelo.', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (188, 47, 'It sets the colour that subsequent shapes will be filled with.', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (139, 47, 'The first 3 arguments to the fill() function are the amount of red, green, and blue to be displayed.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (140, 47, 'The colour specified by the arguments will be used for any subsequent shapes drawn.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (141, 47, 'You can think of this command as selecting a pencil to be used next for next time your draw a shape.', NULL, 3, now(), now());

-- Question 48
insert into skill_question VALUES (48, 3, 1, 'What does the noFill() function do?', NULL, now(), now());
insert into skill_question_option VALUES (189, 48, 'It sets the background of the canvas to black.', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (190, 48, 'It ensures any shapes that are subsequently drawn will not be filled with any colour.', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (191, 48, 'There is not a noFill() function in PyAngelo.', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (192, 48, 'It sets the colour that subsequent shapes will be filled with to black.', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (142, 48, 'When shapes are drawn in PyAngelo you can specify the colour of the shape itself and the colour of the border.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (143, 48, 'There are separate commands that can be used if you do not wish to have a colour for the shape or the border', NULL, 2, now(), now());
insert into skill_question_hint VALUES (144, 48, 'The noFill() function ensures that any subsequent shapes that are drawn will not have a colour.', NULL, 3, now(), now());

-- Question 49
insert into skill_question VALUES (49, 3, 1, 'What does the stroke(r, g, b) function do?', NULL, now(), now());
insert into skill_question_option VALUES (193, 49, 'It sets the background of the canvas to the specified colour.', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (194, 49, 'It sets the colour that subsequent shapes will be filled with.', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (195, 49, 'There is no stroke() function in PyAngelo.', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (196, 49, 'It sets the colour for the border of subsequent shapes that are drawn.', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (145, 49, 'The first 3 arguments to the stroke() function are the amount of red, green, and blue to be displayed.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (146, 49, 'The colour specified by the arguments will be used as the border colour for any subsequent shapes drawn.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (147, 49, 'The stroke() function is used to set the border colour whilst the fill() function is used to set colour the shape is filled with.', NULL, 3, now(), now());

-- Question 50
insert into skill_question VALUES (50, 3, 1, 'What does the noStroke() function do?', NULL, now(), now());
insert into skill_question_option VALUES (197, 50, 'It sets the background of the canvas to black.', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (198, 50, 'There is not a noStroke() function in PyAngelo.', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (199, 50, 'It ensures any shapes that are subsequently drawn will not have a border.', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (200, 50, 'It ensures any shapes that are subsequently drawn will not be filled with any colour.', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (148, 50, 'When shapes are drawn in PyAngelo you can specify the colour of the shape itself and the colour of the border.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (149, 50, 'There are separate commands that can be used if you do not wish to have a colour for the shape or the border', NULL, 2, now(), now());
insert into skill_question_hint VALUES (150, 50, 'The noStroke() function ensures that any subsequent shapes that are drawn will not have a border.', NULL, 3, now(), now());

-- Question 51
insert into skill_question VALUES (51, 3, 1, 'What is the smallest value that can be used when specifying the intensity of each colour using the RGB scheme?', NULL, now(), now());
insert into skill_question_option VALUES (201, 51, '-255', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (202, 51, '0', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (203, 51, '100', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (204, 51, '255', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (151, 51, 'An RGB colour value is specified with rgb (red, green, blue).', NULL, 1, now(), now());
insert into skill_question_hint VALUES (152, 51, 'The intensity of each colour is specified as an integer between 0 and 255.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (153, 51, '0 is the lowest intensity meaning none of that colour will be displayed and 255 is the maximum intensity.', NULL, 3, now(), now());

-- Question 52
insert into skill_question VALUES (52, 3, 1, 'What is the largest value that can be used when specifying the intensity of each colour using the RGB scheme?', NULL, now(), now());
insert into skill_question_option VALUES (205, 52, '-255', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (206, 52, '0', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (207, 52, '100', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (208, 52, '255', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (154, 52, 'An RGB colour value is specified with rgb (red, green, blue).', NULL, 1, now(), now());
insert into skill_question_hint VALUES (155, 52, 'The intensity of each colour is specified as an integer between 0 and 255.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (156, 52, '0 is the lowest intensity meaning none of that colour will be displayed and 255 is the maximum intensity.', NULL, 3, now(), now());

-- Question 53
insert into skill_question VALUES (53, 3, 1, 'What does the value 0 represent in the following line of code?<pre>background(0, 255, 255)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (209, 53, 'That the colour should have no red.', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (210, 53, 'That the colour should have no green.', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (211, 53, 'That the colour should have no blue.', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (212, 53, 'That the colour should have the full intensity of red.', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (157, 53, 'An RGB colour value is specified with rgb (red, green, blue).', NULL, 1, now(), now());
insert into skill_question_hint VALUES (158, 53, 'The intensity of each colour is specified as an integer between 0 and 255.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (159, 53, '0 is the lowest intensity meaning none of that colour will be displayed and 255 is the maximum intensity.', NULL, 3, now(), now());

-- Question 54
insert into skill_question VALUES (54, 3, 1, 'What does the value 255 represent in the following line of code ?<pre>background(0, 0, 255)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (213, 54, 'That the background colour should include the full intensity of red light.', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (214, 54, 'That the background colour should include full intensity of green light.', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (215, 54, 'That the background colour should include full intensity of blue light.', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (216, 54, 'That the background colour should have zero intensity of blue light.', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (160, 54, 'An RGB colour value is specified with rgb (red, green, blue).', NULL, 1, now(), now());
insert into skill_question_hint VALUES (161, 54, 'The intensity of each colour is specified as an integer between 0 and 255.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (162, 54, '0 is the lowest intensity meaning none of that colour will be displayed and 255 is the maximum intensity.', NULL, 3, now(), now());

-- Question 55
insert into skill_question VALUES (55, 3, 1, 'What is the order of colours when calling the background() function?', NULL, now(), now());
insert into skill_question_option VALUES (217, 55, 'red blue green', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (218, 55, 'blue green red', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (219, 55, 'red green blue', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (220, 55, 'green blue red', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (163, 55, 'The background() function requires 3 main arguments.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (164, 55, 'These 3 arguments specify a colour.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (165, 55, 'The 3 arguments are the intensity of red, green, and blue in that order.', NULL, 3, now(), now());

-- Question 56
insert into skill_question VALUES (56, 3, 1, 'What is the order of colours when calling the fill() function?', NULL, now(), now());
insert into skill_question_option VALUES (221, 56, 'red blue green', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (222, 56, 'blue green red', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (223, 56, 'green blue red', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (224, 56, 'red green blue', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (166, 56, 'The background() function requires 3 main arguments.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (167, 56, 'These 3 arguments specify a colour.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (168, 56, 'The 3 arguments are the intensity of red, green, and blue in that order.', NULL, 3, now(), now());

-- Question 57
insert into skill_question VALUES (57, 3, 1, 'How many arguments are supplied to the noStroke() function?', NULL, now(), now());
insert into skill_question_option VALUES (225, 57, '0', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (226, 57, '1', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (227, 57, '2', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (228, 57, '3', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (169, 57, 'The noStroke() function removes any borders from subsequent shapes that are drawn.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (170, 57, 'No colour is specified when calling the noStroke() function.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (171, 57, 'The noStroke() function does not require any arguments.', NULL, 3, now(), now());

-- Question 58
insert into skill_question VALUES (58, 3, 1, 'How many arguments are supplied to the noFill() function?', NULL, now(), now());
insert into skill_question_option VALUES (229, 58, '0', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (230, 58, '1', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (231, 58, '2', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (232, 58, '3', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (172, 58, 'The noFill() function means that any subsequent shapes that are drawn will not be filled with a colour.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (173, 58, 'No colour is specified when calling the noFill() function.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (174, 58, 'The noFill() function does not require any arguments.', NULL, 3, now(), now());

-- Question 59
insert into skill_question VALUES (59, 3, 1, 'What colour will the background be after the following code is run?<pre>background(100, 100, 100)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (233, 59, 'white', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (234, 59, 'grey', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (235, 59, 'black', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (236, 59, 'red', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (175, 59, 'RGB specifies the intensity of red, green, and blue for a colour.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (176, 59, 'When all the intensities are set to the same value the colour will be a shade of grey.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (177, 59, 'The closer to 0 the darker the grey, the closer to 255 the lighter the grey.', NULL, 3, now(), now());

-- Question 60
insert into skill_question VALUES (60, 3, 1, 'What colour will subsequent shapes be filled with after the following line of code is run?<pre>fill(255, 0, 0)</pre>', NULL, now(), now());
insert into skill_question_option VALUES (237, 60, 'red', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (238, 60, 'green', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (239, 60, 'blue', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (240, 60, 'black', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (178, 60, 'RGB specifies the intensity of red, green, and blue for a colour.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (179, 60, 'The colours are specified in order (red, green, blue).', NULL, 2, now(), now());
insert into skill_question_hint VALUES (180, 60, 'Since fill(255, 0, 0) specifies full intensity for red, and zero for green and blue, the colour will be red.', NULL, 3, now(), now());

-- Question 61
insert into skill_question VALUES (61, 4, 1, 'On which line of code is the following error?<pre>NameError: name \'setCanvasize\' is not defined on line 1</pre>', NULL, now(), now());
insert into skill_question_option VALUES (241, 61, '1', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (242, 61, '2', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (243, 61, '3', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (244, 61, 'There is no error', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (181, 61, 'Errors in PyAngelo are reported in the output window.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (182, 61, 'The error message specifies on which line the error occurred.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (183, 61, 'The error message in the question indicates the error is on line 1.', NULL, 3, now(), now());

-- Question 62
insert into skill_question VALUES (62, 4, 1, 'On which line of code is the following error?<pre>SyntaxError: bad input on line 2</pre>', NULL, now(), now());
insert into skill_question_option VALUES (245, 62, 'There is no error', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (246, 62, '10', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (247, 62, '4', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (248, 62, '2', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (184, 62, 'Errors in PyAngelo are reported in the output window.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (185, 62, 'The error message specifies on which line the error occurred.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (186, 62, 'The error message in the question indicates the error is on line 2.', NULL, 3, now(), now());

-- Question 63
insert into skill_question VALUES (63, 4, 1, 'On which line of code is the following error?<pre>TypeError: rect() takes exactly 4 arguments (2 given) on line 3</pre>', NULL, now(), now());
insert into skill_question_option VALUES (249, 63, '2', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (250, 63, '3', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (251, 63, '4', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (252, 63, 'There is no error', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (187, 63, 'Errors in PyAngelo are reported in the output window.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (188, 63, 'The error message specifies on which line the error occurred.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (189, 63, 'The error message in the question indicates the error is on line 3.', NULL, 3, now(), now());

-- Question 64
insert into skill_question VALUES (64, 4, 1, 'How would you fix the following error?', 'errors-01.png', now(), now());
insert into skill_question_option VALUES (253, 64, 'Change the first argument from 0 to 255', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (254, 64, 'Change the function name from backround to background', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (255, 64, 'Change the function name from backround to back', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (256, 64, 'Change all arguments from 0 to 255', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (190, 64, 'Errors in PyAngelo are reported in the output window.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (191, 64, 'The error message informs us that backround is not defined.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (192, 64, 'The actual function name that does exist is background().', NULL, 3, now(), now());

-- Question 65
insert into skill_question VALUES (65, 4, 1, 'How would you fix the following error?', 'errors-02.png', now(), now());
insert into skill_question_option VALUES (257, 65, 'Change the function name from rect to rectangle', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (258, 65, 'Delete line 3', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (259, 65, 'Switch the order of the arguments to the rect function from 20, 40 to 40, 20', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (260, 65, 'Add the width and height arguments to the rect function. Then line 4 might look like: rect(20, 40, 100, 200)', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (193, 65, 'Errors in PyAngelo are reported in the output window.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (194, 65, 'The error message informs us that 4 arguments need to be provided to the rect() function.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (195, 65, 'The four parameters to the rect function are x, y, width, height.', NULL, 3, now(), now());

-- Question 66
insert into skill_question VALUES (66, 4, 1, 'How would you fix the following error?', 'errors-03.png', now(), now());
insert into skill_question_option VALUES (261, 66, 'Change the function name from background to backg', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (262, 66, 'Remove all arguments to the background() function', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (263, 66, 'Set the canvas size to 200 pixels wide by 100 pixels high', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (264, 66, 'Remove any spaces and tabs before the background() function on line 2', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (196, 66, 'The error message informs us that there is a syntax error on line 2.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (197, 66, 'In Python, code is indented to create a block of code.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (198, 66, 'In the program above there is only a single block of code and hence there should not be any indentation.', NULL, 3, now(), now());

-- Question 67
insert into skill_question VALUES (67, 4, 1, 'How would you fix the following error?', 'errors-04.png', now(), now());
insert into skill_question_option VALUES (265, 67, 'Change the function name from circle to circ', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (266, 67, 'Remove all arguments to the circle() function', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (267, 67, 'Move the line 5 up to line 4', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (268, 67, 'Add a third parameter to the circle() function that specifies the radius', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (199, 67, 'The error message informs us that the circle() function takes 2 arguments but only 2 were given.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (200, 67, 'The third parameter to the circle() function is the radius.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (201, 67, 'To fix the error you need to provide the radius as the third argument to the circle() function.', NULL, 3, now(), now());

-- Question 68
insert into skill_question VALUES (68, 4, 1, 'How would you fix the following error?', 'errors-05.png', now(), now());
insert into skill_question_option VALUES (269, 68, 'Remove the third argument of 10 from the circle() function', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (270, 68, 'Add a fourth argument to the circle() function to specify the height of the circle', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (271, 68, 'Change the function name from circ to circle', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (272, 68, 'Delete line 3', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (202, 68, 'The error message informs us that circ is not a known command.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (203, 68, 'The command to draw a circle is circle and it takes 3 arguments.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (204, 68, 'To fix the error you need to change the function name from circ to circle and leave the arguments as they are.', NULL, 3, now(), now());

-- Question 69
insert into skill_question VALUES (69, 4, 1, 'How would you fix the following error?', 'errors-06.png', now(), now());
insert into skill_question_option VALUES (273, 69, 'Remove the second argument of 360 from the setcanvassize() function', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (274, 69, 'Change the function name from setcanvassize to updateCanvasSize', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (275, 69, 'Change the function name from setcanvassize to setCanvasSize', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (276, 69, 'Change the function name from setcanvassize to SetCanvasSize', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (205, 69, 'The error message informs us that setcanvassize is not a known command.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (206, 69, 'Python is a case-sensitive language.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (207, 69, 'The command to set the canvas size in PyAngelo is setCanvasSize.', NULL, 3, now(), now());

-- Question 70
insert into skill_question VALUES (70, 4, 1, 'How would you fix the following error?', 'errors-07.png', now(), now());
insert into skill_question_option VALUES (277, 70, 'Remove the third argument of 0 from the setcanvassize() function', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (278, 70, 'Change the function name from Fill to FillColour', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (279, 70, 'Change the function name from Fill to fill', NULL, 3, 1, now(), now());
insert into skill_question_option VALUES (280, 70, 'Remove the background() function on line 2', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (208, 70, 'The error message informs us that Fill is not a known command.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (209, 70, 'Python is a case-sensitive language.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (210, 70, 'The command to set the fill colour is fill.', NULL, 3, now(), now());

-- Question 71
insert into skill_question VALUES (71, 4, 1, 'What is the purpose of comments in a computer program?', NULL, now(), now());
insert into skill_question_option VALUES (281, 71, 'To provide explanatory information about the code', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (282, 71, 'To alter the speed the code runs at', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (283, 71, 'To tell the computer which progamming langauge to use', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (284, 71, 'Comments have no purpose', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (211, 71, 'Comments are ignored by the computer.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (212, 71, 'Comments in python can be specified by prefixing the line with the # (hash) character.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (213, 71, 'Comments should be used to help humans understand the purpose of the code', NULL, 3, now(), now());

-- Question 72
insert into skill_question VALUES (72, 4, 1, 'Comments in Python begin with which character?', NULL, now(), now());
insert into skill_question_option VALUES (285, 72, '$', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (286, 72, '#', NULL, 2, 1, now(), now());
insert into skill_question_option VALUES (287, 72, '!', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (288, 72, '%', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (214, 72, 'Comments are ignored by the computer.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (215, 72, 'Comments are used to help humans understand the purpose of the code.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (216, 72, 'Comments in python can be specified by prefixing the line with the # (hash) character.', NULL, 3, now(), now());

-- Question 73
insert into skill_question VALUES (73, 4, 1, 'On which line in the following program is a comment?', 'comments-01.png', now(), now());
insert into skill_question_option VALUES (289, 73, 'There are no comments', NULL, 1, 0, now(), now());
insert into skill_question_option VALUES (290, 73, '1', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (291, 73, '2', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (292, 73, '3', NULL, 4, 1, now(), now());
insert into skill_question_hint VALUES (217, 73, 'Comments are ignored by the computer.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (218, 73, 'Comments are used to help humans understand the purpose of the code.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (219, 73, 'Comments in python can be specified by prefixing the line with the # (hash) character.', NULL, 3, now(), now());

-- Question 74
insert into skill_question VALUES (74, 4, 1, 'In the following program, what colour will the rectangle be filled with?', 'comments-02.png', now(), now());
insert into skill_question_option VALUES (293, 74, 'Red', NULL, 1, 1, now(), now());
insert into skill_question_option VALUES (294, 74, 'White', NULL, 2, 0, now(), now());
insert into skill_question_option VALUES (295, 74, 'Black', NULL, 3, 0, now(), now());
insert into skill_question_option VALUES (296, 74, 'No rectangle is drawn', NULL, 4, 0, now(), now());
insert into skill_question_hint VALUES (220, 74, 'Comments are ignored by the computer.', NULL, 1, now(), now());
insert into skill_question_hint VALUES (221, 74, 'The call to the fill function on line 4 is commented out.', NULL, 2, now(), now());
insert into skill_question_hint VALUES (222, 74, 'This means the call to the first fill function on line 3 determines the colour for the rect() function. ', NULL, 3, now(), now());

insert into db_change
values (
  56,
  'Insert coordinate questions.',
  '0056_insert_coordinate_questions.sql',
  now()
);

insert into db_change
values (
  56,
  'Insert coordinate questions.',
  '0056_insert_coordinate_questions.sql',
  now()
);
