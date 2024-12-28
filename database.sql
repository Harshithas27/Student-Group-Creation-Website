
ALTER TABLE users ADD COLUMN team_created BOOLEAN DEFAULT FALSE;


CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollmentno VARCHAR(20) NOT NULL UNIQUE,
    studentname VARCHAR(100) NOT NULL,
    divisioncode CHAR(10) NOT NULL,
    emailid VARCHAR(100) NOT NULL
   
   
);

CREATE TABLE teams (
    team_number INT AUTO_INCREMENT PRIMARY KEY;
    student1_id INT NOT NULL,
    student2_id INT NOT NULL,
    student3_id INT NOT NULL,
    student4_id INT NOT NULL
);

ALTER TABLE teams ADD student1_name VARCHAR(255), ADD student1_enrollmentno VARCHAR(50),
                   ADD student2_name VARCHAR(255), ADD student2_enrollmentno VARCHAR(50),
                   ADD student3_name VARCHAR(255), ADD student3_enrollmentno VARCHAR(50),
                   ADD student4_name VARCHAR(255), ADD student4_enrollmentno VARCHAR(50);


ALTER TABLE teams ADD guide_name VARCHAR(255), 
                   ADD project_name VARCHAR(255);



ALTER TABLE teams ADD student1_divisioncode VARCHAR(255), 
                   ADD student2_divisioncode  VARCHAR(255), 
                   ADD student3_divisioncode  VARCHAR(255), 
                   ADD student4_divisioncode  VARCHAR(255);

ALTER TABLE teams ADD guide_id int not null;

CREATE TABLE guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guide_name VARCHAR(255) NOT NULL
);


CREATE TABLE abstracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uploader_name VARCHAR(255) NOT NULL,
    team_number INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filetype VARCHAR(255) NOT NULL,
    filesize int(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
