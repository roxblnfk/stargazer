-- Create database and user for the stargazer application
CREATE USER stargazer WITH PASSWORD 'stargazer';
CREATE DATABASE stargazer OWNER stargazer;
GRANT ALL PRIVILEGES ON DATABASE stargazer TO stargazer;
