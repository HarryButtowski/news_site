
CREATE DATABASE site
  WITH OWNER = postgres
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'Russian_Russia.1251'
       LC_CTYPE = 'Russian_Russia.1251'
       CONNECTION LIMIT = -1;
	   
CREATE TABLE tbl_lookup
(
	id serial  PRIMARY KEY,
	name VARCHAR(128) NOT NULL,
	code INTEGER NOT NULL,
	type VARCHAR(128) NOT NULL,
	position INTEGER NOT NULL
);

CREATE TABLE tbl_user
(
	id serial  PRIMARY KEY,
	username VARCHAR(128) NOT NULL,
	password VARCHAR(128) NOT NULL,
	email VARCHAR(128) NOT NULL,
	profile TEXT
);

CREATE TABLE tbl_news
(
	id serial PRIMARY KEY,
	title VARCHAR(128) NOT NULL,
        slug VARCHAR(128) NOT NULL,
        preview TEXT NOT NULL,
	content TEXT NOT NULL,
	status INTEGER NOT NULL,
	create_time INTEGER,
	update_time INTEGER,
	author_id INTEGER NOT NULL,
	CONSTRAINT FK_news_author FOREIGN KEY (author_id)
		REFERENCES tbl_user (id) ON DELETE CASCADE ON UPDATE RESTRICT
);
CREATE INDEX tbl_news_author_id_index ON tbl_news (author_id);

CREATE TABLE tbl_category
(
	id serial PRIMARY KEY,
	name VARCHAR(128) NOT NULL,
	parent_id INTEGER DEFAULT 0,
	update_time INTEGER
);
CREATE INDEX tbl_category_parent_id_index ON tbl_category (parent_id);
CREATE INDEX tbl_category_update_time_index ON tbl_category (update_time);  /*for update cache**/

CREATE TABLE tbl_news_category
(
	news_id INTEGER NOT NULL,
        category_id INTEGER NOT NULL,
        CONSTRAINT FK_category FOREIGN KEY (category_id)
                        REFERENCES tbl_category (id) ON DELETE CASCADE ON UPDATE RESTRICT,
        CONSTRAINT FK_news FOREIGN KEY (news_id)
                        REFERENCES tbl_news (id) ON DELETE CASCADE ON UPDATE RESTRICT
);
CREATE INDEX tbl_news_category_news_id_index ON tbl_news_category (news_id);
CREATE INDEX tbl_news_category_category_id_index ON tbl_news_category (category_id);

CREATE TABLE tbl_comment
(
	id serial PRIMARY KEY,
	content TEXT NOT NULL,
	status INTEGER NOT NULL,
	create_time INTEGER,
	author VARCHAR(128) NOT NULL,
	email VARCHAR(128) NOT NULL,
	url VARCHAR(128),
	news_id INTEGER NOT NULL,
	CONSTRAINT FK_comment_news FOREIGN KEY (news_id)
		REFERENCES tbl_news (id) ON DELETE CASCADE ON UPDATE RESTRICT
);
CREATE INDEX tbl_comment_news_id_index ON tbl_comment (news_id);

INSERT INTO tbl_lookup (name, type, code, position) VALUES ('Draft', 'PostStatus', 1, 1);
INSERT INTO tbl_lookup (name, type, code, position) VALUES ('Published', 'PostStatus', 2, 2);
INSERT INTO tbl_lookup (name, type, code, position) VALUES ('Archived', 'PostStatus', 3, 3);
INSERT INTO tbl_lookup (name, type, code, position) VALUES ('Pending Approval', 'CommentStatus', 1, 1);
INSERT INTO tbl_lookup (name, type, code, position) VALUES ('Approved', 'CommentStatus', 2, 2);

INSERT INTO tbl_user (username, password, email) VALUES ('admin','$2a$10$JTJf6/XqC94rrOtzuF397OHa4mbmZrVTBOQCmYD9U.obZRUut4BoC','webmaster@example.com');
INSERT INTO tbl_news (title, slug, preview, content, status, create_time, update_time, author_id) VALUES ('Welcome!','Welcome', 'This blog system is developed using Yii. It is meant to demonstrate how to use Yii to build a complete real-world application.', 'This blog system is developed using Yii. It is meant to demonstrate how to use Yii to build a complete real-world application. Complete source code may be found in the Yii releases.

Feel free to try this system by writing new posts and posting comments.',2,1230952186,1230952187,1);
INSERT INTO tbl_news (title, slug, preview, content, status, create_time, update_time, author_id) VALUES ('A Test Post', 'A_Test_Post', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 2,1230952187,1230952187,1);
INSERT INTO tbl_news (title, slug, preview, content, status, create_time, update_time, author_id) VALUES ('news3','news3', 'This blog system is developed using Yii. It is meant to demonstrate how to use Yii to build a complete real-world application.', 'This blog system is developed using Yii. It is meant to demonstrate how to use Yii to build a complete real-world application. Complete source code may be found in the Yii releases.Feel free to try this system by writing new posts and posting comments.',2,1230952188,1230952187,1);
INSERT INTO tbl_news (title, slug, preview, content, status, create_time, update_time, author_id) VALUES ('news4', 'news4', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 2,1230952189,1230952187,1);


INSERT INTO tbl_comment (content, status, create_time, author, email, news_id) VALUES ('This is a test comment.', 2, 1230952187, 'Tester', 'tester@example.com', 2);

INSERT INTO tbl_category (name, update_time) VALUES ('web',1230952187);
INSERT INTO tbl_category (name, update_time) VALUES ('php',1230952188);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('html', 1, 1230952189);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('css', 1, 1230952190);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('js', 1, 1230952191);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('body', 3, 1230952192);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('table', 6, 1230952193);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('head', 3, 1230952194);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('selectors', 4, 1230952195);
INSERT INTO tbl_category (name, parent_id, update_time) VALUES ('yii', 2, 1230952196);

INSERT INTO tbl_news_category (news_id, category_id) VALUES (1,3);
INSERT INTO tbl_news_category (news_id, category_id) VALUES (2,4);
INSERT INTO tbl_news_category (news_id, category_id) VALUES (3,8);
INSERT INTO tbl_news_category (news_id, category_id) VALUES (4,5);
