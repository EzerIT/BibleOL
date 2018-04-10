CREATE TABLE hints (self integer primary key, hint text);

BEGIN TRANSACTION;

INSERT INTO hints VALUES(508,'ps=p3');
INSERT INTO hints VALUES(1453,'ps=p3');
INSERT INTO hints VALUES(3053,'vt=infc');
INSERT INTO hints VALUES(3062,'vt=infc');
INSERT INTO hints VALUES(3173,'ps=p2');

COMMIT;

