--Usr taulun testidata
INSERT INTO Usr (username, password) VALUES ('User', '$2y$10$hsHXJqwWld1F51vV4oyG9u9BdFl8U82pZlyXuzDiq0JyuZww7CeBC');
INSERT INTO Usr (username, password) VALUES ('User2', '$2y$10$3xPmhXDmrOe13fidat4ImOPtrc/NAjJA94caxmK2hAQXGaf5IfLjK');
INSERT INTO Usr (username, password) VALUES ('User3', '$2y$10$LLn0BLGyGNtR4dxF2dwo7./ax2N/LOlWb8UUZUTKKA3X2m7fXKfGW');
INSERT INTO Usr (username, password) VALUES ('User4', '$2y$10$Qa4XfvMDJFGVQyPgq/8XQO5u2kz6VicvF2JKTn6SHDi6tGGFOJtci');
--Meme taulun testidata
INSERT INTO Meme (poster, title, type, content) VALUES ('User', 'Homer into the bushes', 'Image', 'https://fat.gfycat.com/OptimalThoseFoal.gif');
INSERT INTO Meme (poster, title, type, content) VALUES ('User', 'Lenny Face', 'Copypasta', '( ͡° ͜ʖ ͡°)');
INSERT INTO Meme (poster, title, type, content) VALUES ('User', 'Aziz The Combat Fighter theme song', 'Video', 'WJc7S_UOfl4');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', ':3', 'Copypasta', '(づ｡◕‿‿◕｡)づ');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', 'Table flip', 'Copypasta', '(ノಠ益ಠ)ノ彡┻━┻');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', 'Anti-table flip', 'Copypasta', '┬──┬ ノ( ゜-゜ノ)');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', 'Why not Zoidberg?', 'Copypasta', 'Why not Zoidberg? (/) (°,,°) (/)');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', 'One does not simply explain memes to someone', 'Image', 'http://s2.quickmeme.com/img/98/98b80beaf6a3449cc2410730d33d2ce863814382b83fab21193d119a9168d95e.jpg');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', 'When you are no longer the frog', 'Image', 'http://gag.fm/uploads/posts/t/l-6632.jpg');
INSERT INTO Meme (poster, title, type, content) VALUES ('User2', 'Why not both?', 'Image', 'https://i.kinja-img.com/gawker-media/image/upload/s--sXmOrBZL--/c_fit,fl_progressive,q_80,w_636/1238895886504840879.jpg');
INSERT INTO Meme (poster, title, type, content) VALUES ('User', 'The wrong dongerhood', 'Copypasta', '༼ ºل͟º༼ ºل͟º༼ ºل͟º ༽ºل͟º ༽ºل͟º ༽ YOU CAME TO THE WRONG DONGERHOOD༼ ºل͟º༼ ºل͟º༼ ºل͟º ༽ºل͟º ༽ºل͟º ༽');
--Favourite taulun testidata
INSERT INTO Favourite (username, meme_id) VALUES ('User', '2');
INSERT INTO Favourite (username, meme_id) VALUES ('User', '3');
INSERT INTO Favourite (username, meme_id) VALUES ('User2', '4');
INSERT INTO Favourite (username, meme_id) VALUES ('User2', '7');
--Comment taulun testidata
INSERT INTO Comment (poster, parent_meme, message, posted, edited) VALUES ('User', '1', 'Hello comment world!', 'now', 'now');
INSERT INTO Comment (poster, parent_meme, message, posted) VALUES ('User2', '1', 'Nice me.me', 'now');