CREATE TABLE settings (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(128) NOT NULL,
  type varchar(128) NOT NULL,
  value TEXT NOT NULL
);

INSERT INTO settings (name, type, value) VALUES ('website_name', 'string', 'AdvancedReporter');
INSERT INTO settings (name, type, value) VALUES ('website_favicon', 'string', 'assets/img/favicon.png');
INSERT INTO settings (name, type, value) VALUES ('website_description', 'string', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed turpis libero, imperdiet sed fermentum quis, tincidunt viverra nunc.');
INSERT INTO settings (name, type, value) VALUES ('website_image', 'string', 'assets/img/main/wallpaper_1.jpg');
INSERT INTO settings (name, type, value) VALUES ('website_scheme', 'string', 'http://');
INSERT INTO settings (name, type, value) VALUES ('website_domain', 'string', 'localhost');
INSERT INTO settings (name, type, value) VALUES ('table_name', 'string', 'advancedreporterreports');
INSERT INTO settings (name, type, value) VALUES ('enable_mailer', 'int', '0');
INSERT INTO settings (name, type, value) VALUES ('website_email', 'string', 'example@example.com');
INSERT INTO settings (name, type, value) VALUES ('smtp_host', 'string', 'smtp1.example.com');
INSERT INTO settings (name, type, value) VALUES ('smtp_username', 'string', 'user@example.com');
INSERT INTO settings (name, type, value) VALUES ('smtp_password', 'string', 'secret');
INSERT INTO settings (name, type, value) VALUES ('smtp_port', 'int', '587');
INSERT INTO settings (name, type, value) VALUES ('session_timeout', 'int', '21600');
INSERT INTO settings (name, type, value) VALUES ('timezone', 'string', 'Europe/Bucharest');
INSERT INTO settings (name, type, value) VALUES ('default_avatar', 'string', 'assets/img/main/default_avatar.png');

CREATE TABLE users (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  username varchar(128) NOT NULL,
  ign varchar(128) NOT NULL,
  email varchar(200) NOT NULL,
  password varchar(200) NOT NULL,
  secret_key varchar(128) NOT NULL,
  avatar TEXT NOT NULL,
  is_admin int(1) NOT NULL,
  cc_users int(1) NOT NULL,
  resolved_reports TEXT NOT NULL,
  uid varchar(128) NOT NULL
);

/*
  >> Email: demo@example.com
  >> Password: demo123
*/

INSERT INTO users (username, ign, email, password, secret_key, avatar, is_admin, cc_users, resolved_reports, uid) VALUES ('Demo', 'reporter', 'demo@example.com', '$SHA$DYzJbGid7JwPi7Zq$9c7cd7b8afd1e8b1eeae8800cdb2f933d34b2aefcdb186e3048fe12333c74f1b', 'de5ff72c6586f6ed4b7a8d6e6d54e008548041a46d7d29ddc5d6428f651121e3', 'default', '1', '1', '', 'user_5c526c0a7619a1ea7689e4');

CREATE TABLE pw_reminder (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  email varchar(200) NOT NULL,
  date varchar(16) NOT NULL,
  ip varchar(200) NOT NULL,
  pwr_key varchar(128) NOT NULL
);