# Phi Sigma Pi National Honor Fraternity
## CS-4380 Final Project (Spring 2018)
* Andrew Pizzullo
* Cody Mcilvaine
* James Grady
* Hunter Ginther

## Introduction:
> Discuss the application domain, your client, the data, and any potential for product commercialization. Also include the URL for your project as well as sample (working) usernames and passwords for each type of user in your system.

Our group has been tasked with creating a functioning website to easily access and manipulate data for the Mizzou chapter of Phi Sigma Pi National Honor Fraternity. Currently, many different tools are being used by the fraternity such as OrgSync, Facebook, Google Docs, and random Excel sheets. Hunter has gathered all the necessary attributes we need to create dummy data for the fraternity since we will need to keep the actual fraternity member data private.

**URL:** [https://web.dsa.missouri.edu/~s18group03/](https://web.dsa.missouri.edu/~s18group03/index.php)

**Sample Credentials:**
```
username  ||  password
----------------------
test324   ||  something
test123   ||  another
```

## ERD:
> Must include an ERD in any type of notation (Chen, Crow’s Foot notation, or other variants). You should explain all constraints and provide DDL for all tables.

![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/ERD.png "ERD")

### ERD table connections:
* Member contact info and address are both one to one with the member table as all members only need one entry in each of these tables. The emergencyContact table is the same.
* The member table references the member status table which is filled with the different types of members(active, inactive, nationally inactive, or alumni). One member status can be references to multiple members.
* familyTree_roster is a one to many with the member table because it will reference many members for each family. It also has a family_id referenced from the family table.
* Each member is referenced to one class_roster which references a specific class.
* Each member can attend multiple events with each event being a difference point type. Event_attendence is used to mark which events a member has gone to.
* Each chair position can make many events, thus the one to many between chair position and the events table.
* Each chair position has the opportunity to create a budget_register with a reference to a budget_item_id.
* Each member can have multiple merch_orders with each merch_order having a reference to a specific piece of merchandise from the merch table.
* Each member has to have member_dues and the option to set up a payment plan.
* A member can be a part of zero or many team_rosters which references one recSports_team. recSports_teams references one specific recSports.
* The votingResults table references one potential member from the potential_roster table.

### Table DDL:
### member_status table
```sql
CREATE TABLE member_status (
    status_id INTEGER PRIMARY KEY,
    status_name VARCHAR(25)
);
```

### member table
```sql
CREATE TABLE member (
    student_id INTEGER PRIMARY KEY,
    pawprint VARCHAR(25),
    salted_password VARCHAR(255),
    status INTEGER REFERENCES member_status (status_id),
    last_name VARCHAR(50),
    first_name VARCHAR(50),
    birthday DATE,
    chapter_book_number INTEGER
);
```

### member_address table
```sql
CREATE TABLE member_address (
    student_id INTEGER REFERENCES member (student_id) ON DELETE CASCADE,
    street_address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(100)
);
```

### member_contact table
```sql
CREATE TABLE member_contact (
    student_id INTEGER REFERENCES member (student_id) ON DELETE CASCADE,
    email_address VARCHAR(255),
    phone_number VARCHAR(25)
);
```

### emergencyContact table
```sql
CREATE TABLE emergencyContact (
    student_id INTEGER REFERENCES member (student_id) ON DELETE CASCADE,
    last_name VARCHAR(50),
    first_name VARCHAR(50),
    email_address VARCHAR(255),
    phone_number VARCHAR(25),
    backup_phone_number VARCHAR(25),
    street_address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(100),
    primary_healthcare_provider VARCHAR(255),
    blood_type VARCHAR(25),
    medical_conditions VARCHAR(255),
    medications VARCHAR(255),
    allergies VARCHAR(255),
    PRIMARY KEY (student_id)
);
```

### memberDues table
```sql
CREATE TABLE memberDues (
    student_id INTEGER UNIQUE REFERENCES member (student_id) ON DELETE CASCADE,
    dues_id SERIAL UNIQUE,
    amount_owed REAL,
    amount_paid REAL,
    payment_method VARCHAR(25),
    notes VARCHAR(255),
    PRIMARY KEY (student_id, dues_id)
);
```

### dues_paymentPlan table
```sql
CREATE TABLE dues_paymentPlan (
    dues_id INTEGER REFERENCES memberDues (dues_id) ON DELETE CASCADE,
    start_date DATE,
    end_date DATE,
    last_amount_paid REAL,
    last_payment_date DATE,
    next_amount_due REAL,
    next_payment_date DATE,
    PRIMARY KEY (dues_id)
);
```

### family table
```sql
CREATE TABLE family (
    family_id SERIAL PRIMARY KEY,
    family_name VARCHAR(255),
    founding_date DATE
);
```

### familyTree_roster table
```sql
CREATE TABLE familyTree_roster (
    student_id INTEGER UNIQUE REFERENCES member (student_id) ON DELETE CASCADE,
    family_id INTEGER NOT NULL REFERENCES family (family_id) ON DELETE CASCADE
);
```

### recSports table
```sql
CREATE TABLE recSports (
    sport_id SERIAL PRIMARY KEY,
    sport_name VARCHAR(255) NOT NULL,
    semester VARCHAR(50),
    start_date DATE,
    end_date DATE
);
```

### recSports_teams table
```sql
CREATE TABLE recSports_teams (
    team_id SERIAL PRIMARY KEY,
    sport_id INTEGER REFERENCES recSports (sport_id),
    team_name VARCHAR(255),
    day_of_week VARCHAR(50)
);
```

### team_roster table
```sql
CREATE TABLE team_roster (
    team_id INTEGER REFERENCES recSports_teams (team_id),
    student_id INTEGER REFERENCES member (student_id),
    PRIMARY KEY (team_id, student_id)
);
```

### merch table
```sql
CREATE TABLE merch (
    merch_id SERIAL PRIMARY KEY,
    item_name VARCHAR(100),
    price REAL,
    semester VARCHAR(50),
    year INTEGER
);
```

### merch_order table
```sql
CREATE TABLE merch_order (
    merch_id INTEGER REFERENCES merch (merch_id) ON DELETE CASCADE,
    student_id INTEGER REFERENCES member (student_id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL,
    payment_due_date DATE,
    delivery_date DATE,
    completed BOOLEAN,
    PRIMARY KEY (merch_id, student_id)
);
```

### budget table
```sql
CREATE TABLE budget (
    budget_item_id SERIAL PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    item_type INTEGER NOT NULL, /* 0 for expense, 1 for revenue */
    budget_amount REAL NOT NULL,
    semester VARCHAR(255),
    year INTEGER
);
```

### chairPosition table
```sql
CREATE TABLE chairPosition (
    position_id INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES member (student_id) ON DELETE CASCADE,
    position_name VARCHAR(255) NOT NULL,
    position_type VARCHAR(255), /* EBOARD or EBEC */
    description VARCHAR(255)
);
```

### archive_chairPosition table
```sql
CREATE TABLE archive_chairPosition (
    archive_id SERIAL PRIMARY KEY,
    position_id INTEGER,
    student_id INTEGER REFERENCES member (student_id) ON DELETE CASCADE,
    year INTEGER
);
```

### budget_register table
```sql
CREATE TABLE budget_register (
    register_id SERIAL PRIMARY KEY,
    budget_item_id INTEGER REFERENCES budget (budget_item_id) NOT NULL,
    chairposition INTEGER REFERENCES chairPosition (position_id),
    transaction_type INTEGER NOT NULL, /* 0 for expense, 1 for revenue */
    transaction_amount REAL NOT NULL,
    transaction_date DATE NOT NULL,
    method VARCHAR(255),
    notes VARCHAR(255)
);
```

### point_type table
```sql
CREATE TABLE point_type (
    type_id INTEGER PRIMARY KEY,
    name VARCHAR(255)
);
```

### events table
```sql
CREATE TABLE events (
    event_id SERIAL PRIMARY KEY,
    position_id INTEGER REFERENCES chairPosition (position_id),
    point_type INTEGER NOT NULL REFERENCES point_type (type_id),
    num_of_points INTEGER NOT NULL,
    title VARCHAR(255), /* Name of the event */
    event_date DATE,
    semester VARCHAR(255)
);
```

### event_attendance table
```sql
CREATE TABLE event_attendance (
    event_id INTEGER REFERENCES events (event_id),
    student_id INTEGER REFERENCES member (student_id),
    point_type INTEGER REFERENCES point_type (type_id),
    PRIMARY KEY (event_id, student_id)
);
```

### potential_roster table
```sql
CREATE TABLE potential_roster (   
    student_id INTEGER PRIMARY KEY,
    pawprint VARCHAR(25),
    last_name VARCHAR(50),
    first_name VARCHAR(50),
    email_address VARCHAR(255),
    phone_number VARCHAR(25)
);
```

### votingResults table
```sql
CREATE TABLE votingResults (
    roundNum INTEGER,
    student_id INTEGER REFERENCES potential_roster (student_id) ON DELETE CASCADE,
    yes INTEGER,
    no INTEGER,
    abstain INTEGER,
    PRIMARY KEY (student_id, roundNum)
);
```

### classes table
```sql
CREATE TABLE classes (
    class_id SERIAL PRIMARY KEY,
    class_name VARCHAR(255),
    semester VARCHAR(50),
    year INTEGER
);
```

### class_roster table
```sql
CREATE TABLE class_roster (
    student_id INTEGER UNIQUE REFERENCES member (student_id) ON DELETE CASCADE,
    class_id INTEGER NOT NULL REFERENCES classes (class_id) ON DELETE CASCADE
);
```

## Queries:
> List at least 20 useful queries (with justification) for your system in natural language and SQL.

### 1. ALL Member info based on id
```sql
SELECT
    M.student_id,
    M.pawprint,
    M.first_name,
    M.last_name,
    M.birthday,
    M.chapter_book_number,
    MS.status_name,
    MA.street_address,
    MA.city,
    MA.state,
    MA.zip_code,
    MC.email_address,
    MC.phone_number
FROM
    member_status MS,
    member M
LEFT JOIN member_address MA ON M.student_id = MA.student_id
LEFT JOIN member_contact MC ON M.student_id = MC.student_id
WHERE
    M.student_id = 1 AND /* Sub in student id where number is */
    M.status = MS.status_id
;
```

### 2. memberDues for member based on id
```sql
SELECT
    MD.amount_owed,
    MD.amount_paid,
    MD.payment_method,
    MD.notes
FROM
    member M
    LEFT JOIN memberDues MD ON M.student_id = MD.student_id
WHERE
    M.student_id = 1
;
```

### 3. member_paymentPlan for member based on id
```sql
SELECT
    DPP.last_amount_paid,
    DPP.last_payment_date,
    DPP.next_amount_due,
    DPP.next_payment_date
FROM
    member M,
    memberDues MD
    LEFT JOIN dues_paymentPlan DPP ON MD.dues_id = DPP.dues_id
WHERE
    M.student_id = 81 AND
    M.student_id = MD.student_id
;
```

### 4. All member points based on id
```sql
SELECT
    PT.name AS point_type,
    SUM(E.num_of_points) AS num_of_points,
    COUNT(*) AS num_of_events_attended
FROM
    member M,
    events E,
    event_attendance EA,
    point_type PT
WHERE
    M.student_id = 1 AND
    M.student_id = EA.student_id AND
    EA.event_id = E.event_id AND
    E.point_type = PT.type_id
GROUP BY
    E.point_type, PT.name
;
```

### 5. All member emergency contact info
```sql
SELECT
    EC.last_name,
    EC.first_name,
    EC.email_address,
    EC.phone_number,
    EC.backup_phone_number,
    EC.street_address,
    EC.city,
    EC.state,
    EC.zip_code,
    EC.primary_healthcare_provider,
    EC.blood_type,
    EC.medical_conditions,
    EC.medical_conditions,
    EC.medications,
    EC.allergies
FROM
    member M
    LEFT JOIN emergencyContact EC ON M.student_id = EC.student_id
WHERE
    M.student_id = 100
;
```

### 6. All merch orders for member based on id
```sql
SELECT
    MCH.item_name,
    MCH.price,
    MO.quantity,
    (MCH.price * MO.quantity) AS total_amount_due,
    MO.payment_due_date,
    MO.delivery_date
FROM
    member M
    LEFT JOIN merch_order MO ON M.student_id = MO.student_id
    LEFT JOIN merch MCH ON MO.merch_id = MCH.merch_id
WHERE
    M.student_id = 1 AND
    MO.completed = false
;
```

### All sports teams for member based on id

#### 7. Active Teams
```sql
SELECT
    RS.sport_name,
    RST.team_name,
    RST.day_of_week,
    RS.semester,
    RS.start_date,
    RS.end_date,
    current_date
FROM
    member M,
    team_roster TR,
    recSports_teams RST,
    recSports RS
WHERE
    M.student_id = 1 AND
    M.student_id = TR.student_id AND
    TR.team_id = RST.team_id AND
    RST.sport_id = RS.sport_id AND
    RS.end_date >= current_date
;
```

#### 8. Old Teams
```sql
SELECT
    RS.sport_name,
    RST.team_name,
    RST.day_of_week,
    RS.semester,
    RS.start_date,
    RS.end_date,
    current_date
FROM
    member M,
    team_roster TR,
    recSports_teams RST,
    recSports RS
WHERE
    M.student_id = 1 AND
    M.student_id = TR.student_id AND
    TR.team_id = RST.team_id AND
    RST.sport_id = RS.sport_id AND
    RS.end_date <= current_date
;
```

### 9. Roster Page Query (Grab all members and basic contact information)
```sql
SELECT
    M.pawprint,
    M.first_name,
    M.last_name,
    MC.email_address,
    MC.phone_number
FROM
    member M
    LEFT JOIN member_contact MC ON M.student_id = MC.student_id
;
```

### 10. All member points grouped by student_id and then point type (name, point_type, num_of_point per type)
```sql
SELECT
    m.student_id,
    m.last_name,
    m.first_name,
    PT.name,
    sub.sum AS num_of_points
FROM
    member AS m,
    (
        SELECT
            ea.student_id,
            p.type_id,
            SUM(e.num_of_points)
        FROM
            events AS e,
            event_attendance AS ea,
            point_type AS p
        WHERE
            e.event_id = ea.event_id AND
            ea.point_type = p.type_id
        GROUP BY (ea.student_id, p.type_id)
    ) AS sub,
    point_type PT
WHERE
    m.student_id = sub.student_id AND
    PT.type_id = sub.type_id
ORDER BY (M.student_id)
;
```

### 11. All members with chair positions
```sql
SELECT
    M.first_name,
    M.last_name,
    CP.position_name
FROM
    member M,
    chairPosition CP
WHERE
    M.student_id = CP.student_id
ORDER BY (CP.position_id)
;
```

### 12. All payment plans for each member
```sql
SELECT
    MD.student_id,
    M.last_name,
    M.first_name,
    DP.last_amount_paid,
    DP.next_amount_due,
    DP.last_payment_date,
    DP.next_payment_date
FROM
    memberDues AS MD,
    dues_paymentPlan AS DP,
    member AS M
WHERE
    MD.dues_id = DP.dues_id AND
    M.student_id = MD.student_id
;
```

### 13. All Merch orders that have not yet been completed
```sql
SELECT
    M.first_name,
    M.last_name,
    Mch.item_name,
    Mch.price
FROM
    member M,
    merch Mch,
    merch_order MO
WHERE
    M.student_id = MO.student_id AND
    Mch.merch_id = MO.merch_id AND
    MO.completed = false
;
```

### 14. Print out a family with roster
```sql
SELECT
    M.student_id,
    M.pawprint,
    M.first_name,
    M.last_name,
    F.family_id,
    F.family_name
FROM
    member M,
    familyTree_roster FTR,
    family F
WHERE
    M.student_id = FTR.student_id AND
    FTR.family_id = F.family_id AND
    FTR.family_id = 2
;
```

### 15. Number of members that attended each event
```sql
SELECT
    E.event_id,
    P.name,
    E.title,
    SUB.count
FROM
    (
        SELECT
            EA.event_id,
            count(EA.student_id)  
        FROM
            event_attendance EA,
            events E
        WHERE
            E.event_id = EA.event_id
        GROUP BY EA.event_id
    ) AS SUB,
    events E,
    point_type P
WHERE
    E.event_id = SUB.event_id AND
    E.point_type = P.type_id AND
    date_part('year', E.event_date) = date_part('year', current_date) AND
    E.semester = 'Spring'
;
```

### 16. Display budget info for specific chair
```sql
SELECT
    CP.position_name,
    B.budget_amount,
    TE.totalexpenses,
    TR.totalrevenues,
    (B.budget_amount - (
        CASE
            WHEN TE.totalexpenses IS NULL THEN 0
            ELSE TE.totalexpenses
        END
        +
        CASE
            WHEN TR.totalrevenues IS NULL THEN 0
            ELSE TR.totalrevenues
        END
    )) AS BudgetRemaining
FROM
    budget B,
    budget_register BR,
    chairPosition CP,
    (
        SELECT
            SUM(transaction_amount) AS TotalRevenues
        FROM
            budget_register BR,
            budget B
        WHERE
            BR.transaction_type = 1 AND /* Revenues */
            BR.budget_item_id = B.budget_item_id AND
            date_part('year',BR.transaction_date) = date_part('year',current_date) AND
            B.semester = 'Spring' AND
            BR.chairposition = 16
    ) AS TR,
    (
        SELECT
            SUM(transaction_amount) AS TotalExpenses
        FROM
            budget_register BR,
            budget B
        WHERE
            BR.transaction_type = 0 AND /* Expense */
            BR.budget_item_id = B.budget_item_id AND
            date_part('year',BR.transaction_date) = date_part('year',current_date) AND
            B.semester = 'Spring' AND
            BR.chairposition = 16
    ) AS TE
WHERE
    B.budget_item_id = BR.budget_item_id AND
    B.year = date_part('year', current_date) AND
    B.semester = 'Spring' AND
    BR.chairposition = CP.position_id AND
    BR.chairposition = 16
LIMIT(1)
/* LIMIT(1) is because GROUP BY would be too expensive of a command and the sub queries make it so that each transaction
    has its own entry so the same information is just repeated multiple times
*/
;
```

### 17. Display class rosters
```sql
SELECT
    CR.student_id,
    M.first_name,
    M.last_name,
    C.class_name,
    C.semester,
    C.year
FROM
    classes C,
    class_roster CR,
    member M
WHERE
    M.student_id = CR.student_id AND
    C.class_id = CR.class_id AND
    C.class_id = 2
```

### 18. All upcoming events for a specific point_type/chairPosition
```sql
SELECT
    E.event_id,
    E.title,
    PT.name AS point_type,
    E.num_of_points,
    E.event_date
FROM
    events E,
    point_type PT
WHERE
    E.point_type = PT.type_id AND
    E.event_date >= current_date AND
    /*E.position_id = 16*/
    E.point_type = 1
```

### 19. Most Active Member (the member with the most points) (Top 5 to see more)
```sql
SELECT
    M.student_id,
    M.first_name,
    M.last_name,
    MA.num_of_points
FROM
    member M,
    (
        SELECT
            m.student_id,
            SUM(sub.sum) AS num_of_points
        FROM
            member AS m,
            (
                SELECT
                    ea.student_id,
                    p.type_id,
                    SUM(e.num_of_points)
                FROM
                    events AS e,
                    event_attendance AS ea,
                    point_type AS p
                WHERE
                    e.event_id = ea.event_id AND
                    ea.point_type = p.type_id
                GROUP BY (ea.student_id, p.type_id)
            ) AS sub,
            point_type PT
        WHERE
            m.student_id = sub.student_id AND
            PT.type_id = sub.type_id
        GROUP BY (M.student_id)
        ORDER BY (num_of_points) DESC
        LIMIT(5)
    ) AS MA
WHERE
    MA.student_id = M.student_id
;
```

### 20. All potential members (Roster of potential members)
```sql
SELECT
    PR.student_id,
    PR.pawprint,
    PR.first_name,
    PR.last_name,
    PR.email_address
FROM
    potential_roster PR
```

### 21. All Potential Members that passed a specific Round (got more than 50% yes)
```sql
WITH percentage AS (
    SELECT
        VR.student_id,
        VR.roundNum,
        round((VR.yes::decimal  / (VR.yes + VR.no + VR.abstain)::decimal), 2) * 100 AS yes_percentage
    FROM
        votingResults VR
)
SELECT
    VR.roundNum,
    VR.student_id,
    M.first_name,
    M.last_name,
    P.yes_percentage
FROM
    votingResults VR,
    percentage P,
    member M
WHERE
    M.student_id = VR.student_id AND
    P.student_id = VR.student_id AND
    P.roundNum = VR.roundNum AND
    /* roundNum and yes_percentage selections should be user inputed */
    VR.roundNum = 1 AND
    P.yes_percentage >= 50
  ```

## Analytics:
> List the top 5 useful analytic functions (with justification) for your system in natural language and SQL in addition to the 20 useful queries.

### 1. Grab percentages of budget items compared to total budget (pie chart)
#### Expenses in Budget
```sql
SELECT
    B.budget_item_id,
    B.item_name,
    B.budget_amount,
    (B.budget_amount / T.total) AS percentage,
    T.total
FROM
    budget AS B,
    (SELECT
         SUM(budget_amount) AS total
     FROM
         budget
     WHERE
         year = 2017 AND /* Input field */
         semester = 'Fall' AND /* Input field */
         item_type = 0
    ) AS T
WHERE
    /*B.year = date_part('year',current_date) AND*/
    /* Year and Semester should be selectable on the front end side */
    B.year = 2017 AND /* Input field */
    B.semester = 'Fall' AND /* Input field */
    B.item_type = 0
```

#### Revenues in Budget
```sql
SELECT
    B.budget_item_id,
    B.item_name,
    B.budget_amount,
    (B.budget_amount / T.total) AS percentage,
    T.total
FROM
    budget AS B,
    (SELECT
         SUM(budget_amount) AS total
     FROM
         budget
     WHERE
         year = 2017 AND /* Input field */
         semester = 'Fall' AND /* Input field */
         item_type = 1
    ) AS T
WHERE
    /*B.year = date_part('year',current_date) AND*/
    /* Year and Semester should be selectable on the front end side */
    B.year = 2017 AND /* Input field */
    B.semester = 'Fall' AND /* Input field */
    B.item_type = 1
```

### 2. Number of Active, Inactive, Nationally Inactive, Alumni members (bar graph)
```sql
SELECT
    MS.status_name,
    COUNT(M.status)
FROM
    member M,
    member_status MS
WHERE
    MS.status_id = M.status
GROUP BY
    M.status, MS.status_name
ORDER BY
    MS.status_name
```

### 3. Number of each Event DAS/fellowshipt/etc. (bar graph)
```sql
SELECT
    P.name,
    COUNT(*)
FROM
    events E,
    point_type P
WHERE
    E.point_type = P.type_id
GROUP BY
    E.point_type, P.name
ORDER BY
    /*COUNT(*) DESC*/
    P.name
```

### 4. Most popular merch items by number of orders (top 5) (bar graph)
```sql
SELECT
    M.item_name,
    M.price,
    M.semester,
    M.year,
    count
FROM
    merch M
JOIN (
    SELECT
        MO.merch_id,
        COUNT(*)
    FROM
        merch_order MO,
        merch M
    WHERE
        M.merch_id = MO.merch_id
    GROUP BY
        MO.merch_id
    ORDER BY
        COUNT(*) DESC
) AS SUB ON SUB.merch_id = M.merch_id
LIMIT(5) /* Could insert any number (possibly make this selectable) */
```

### 5. Averages of Yes, No, Abstain in votingResults (round 1, 2, 3, or all) (line/pie chart)
#### Average of each round
```sql
SELECT
    roundNum,
    TRUNC(AVG(yes), 2) AS avg_yes,
    TRUNC(AVG(no), 2) AS avg_no,
    TRUNC(AVG(abstain), 2) AS avg_abstain
FROM
    votingResults VR
GROUP BY
    VR.roundNum
```
#### Average of all rounds
```sql
SELECT
    TRUNC(AVG(yes), 2) AS avg_yes,
    TRUNC(AVG(no), 2) AS avg_no,
    TRUNC(AVG(abstain), 2) AS avg_abstain
FROM
    votingResults VR
```

## Normalization:
> Provide an in-depth discussion of normalization, dependency preservation, and de-normalization with respect to your project.

## Indexing:
> Give an in-depth discussion of indexing as it relates to your project
including how/why you created specific additional indexes. Provide the associated SQL statement for creation of those indexes.

### member Table (student_id, first_name, last_name)
```sql
CREATE INDEX member_name ON member (student_id, first_name, last_name);
```
> Most queries include the members table so creating this index will increase the speed of our queries.

### memberDues Table (amount_owed, amount_paid)
```sql
CREATE INDEX member_dues_amount ON memberDues (amount_owed, amount_paid);
```
> All members pay dues, so this index will increase the speed of those queries.

### budget Table (budget_item_id, semester, year)
```sql
CREATE INDEX budget_index ON budget (budget_item_id, item_type, semester, year);
```
> This table can get large over the years, so having an index on it is necessary for the long run.

### Potential_roster Table (student_id, first_name, last_name)
```sql
CREATE INDEX potential_roster_name ON potential_roster (student_id, first_name, last_name);
```
> This table can be large during the beginning of the semester so it is important to have an index on this table.


## Optimization and Tuning:
> Discuss the topics covered in class, specifically how they impacted the design of your final project.

## Security setting:
> Discuss the topics covered in class, specifically how they impacted your final project (e.g. any views you created for your project, including the SQL statement used to create the view; discretionary access control setting, etc.).

We used mandatory access control on the front end for our application. Once users login with their credentials, they will have access to every page they should have. We control this through the use of a session variable that is assigned to each user upon login. Every member will be shown the main dashboard page and then members who have positions will have a separate page they can access that is particular to their function. For example, the Fellowship Chair will have access to a form page where they can create Fellowship Events. The backend does not really deal with this as it is really only concerned with things such as invalid form inputs and SQL injection since much of the code is built into the front end. We did not need to create any views as different queries were made in combination with teh mandatory access control security system to display and access proper data. As far as handling the input on these forms, checks are made to make sure all the fields are filled out and are the proper data type. We also check to make sure there is no duplicate data so we make sure the same event name has not already been created for the same semester and year. These considerations had a huge impact on the delivery of our final project. We kept security in our thoughts first before every move as we know how crucial that is to any kind of application that will be holding sensitive user data. Not only did the data need to be protected from the world in terms of SQL injection, but it also needed to be protected within the chapter since some information is vital to limit to certain positions. If we had to put a number to it, we think close to 20% of our time went into security measures.

## Other topics:
> If you included any other database-related items in your project (triggers, PL/SQL, SparkQL, etc.), you should provide codes and discuss their use.

### Triggers:

#### dues_paymentPlan table
* Trigger: When last_amount_paid updated -> update amount_paid in memberDues
    * amount_paid = (OLD)amount_paid + (NEW)last_amount_paid

```sql
DROP TRIGGER IF EXISTS dues_paymentPlan_payment ON dues_paymentPlan;
DROP FUNCTION IF EXISTS payment_made();
```
```sql
CREATE OR REPLACE FUNCTION payment_made()
 RETURNS trigger AS
$$
BEGIN
    IF NEW.last_amount_paid IS NOT NULL
    THEN
        UPDATE memberDues
        SET amount_paid = amount_paid + NEW.last_amount_paid
        WHERE memberDues.dues_id = OLD.dues_id;

    END IF;

    IF OLD.last_payment_date = NEW.last_payment_date OR NEW.last_payment_date IS NULL
    THEN
        UPDATE dues_paymentPlan
        SET last_payment_date = current_date
        WHERE dues_paymentPlan.dues_id = OLD.dues_id;

    END IF;

    RETURN NEW;
END;
$$
LANGUAGE plpgsql;
```
```sql
CREATE TRIGGER dues_paymentPlan_payment
  AFTER UPDATE OF last_amount_paid
  ON dues_paymentPlan
  FOR EACH ROW
  EXECUTE PROCEDURE payment_made();
```

#### events table
* Trigger: When point_type is updated event_attendance should be updated
    * events.point_type = event_attendance.point_type
    * or memberPoint_associative

```sql
DROP TRIGGER IF EXISTS event_point_type_update ON events;
DROP FUNCTION IF EXISTS event_type_update();
```
```sql
CREATE OR REPLACE FUNCTION event_type_update()
 RETURNS trigger AS
$$
BEGIN
    UPDATE event_attendance
    SET point_type = NEW.point_type
    WHERE event_id = OLD.event_id;

    RETURN NEW;
END;
$$
LANGUAGE plpgsql;
```
```sql
CREATE TRIGGER event_point_type_update
  AFTER UPDATE OF point_type
  ON events
  FOR EACH ROW
  EXECUTE PROCEDURE event_type_update();
```

#### chairPosition Update on student ID
* create new archive chairPosition Table
* when student_id is updated create new entry in archive table and then update student_id

```sql
DROP TRIGGER IF EXISTS archive_chairPosition ON chairPosition;
DROP FUNCTION IF EXISTS position_archive();
```
```sql
CREATE OR REPLACE FUNCTION position_archive()
 RETURNS trigger AS
$$
BEGIN
    INSERT INTO archive_chairPosition(position_id,student_id,year) VALUES
        (
            OLD.position_id,
            OLD.student_id,
            date_part('year',current_date)
        );

    RETURN NEW;
END;
$$
LANGUAGE plpgsql;
```
```sql
CREATE TRIGGER archive_chairPosition
  AFTER UPDATE OF student_id
  ON chairPosition
  FOR EACH ROW
  EXECUTE PROCEDURE position_archive();
```

## User’s manual:
> Provide a manual that allows anyone to learn how to operate every aspect of your project. Include screenshots of interfaces and step-by-step instructions. with interfaces and instructions to use your system.
