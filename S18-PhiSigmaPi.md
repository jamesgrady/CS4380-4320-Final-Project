# Phi Sigma Pi National Honor Fraternity
## CS-4380 Final Project (Spring 2018)
* Andrew Pizzullo
* Cody Mcilvaine
* James Grady
* Hunter Ginther

## Introduction:
> Discuss the application domain, your client, the data, and any potential for product commercialization. Also include the URL for your project as well as sample (working) usernames and passwords for each type of user in your system.

Our group has been tasked with creating a functioning website to easily access and manipulate data for the Mizzou chapter of Phi Sigma Pi National Honor Fraternity. Currently, many different tools are being used by the fraternity such as OrgSync, Facebook, Google Docs, and random Excel sheets. Hunter has gathered all the necessary attributes we need to create dummy data for the fraternity since we will need to keep the actual fraternity member data private. There isn't really any potential for product commercialization, however, the application could be manipulated to account for other school chapters or even Nationals.

**URL:** [https://web.dsa.missouri.edu/~s18group03/](https://web.dsa.missouri.edu/~s18group03/index.php)

**Sample Credentials:**
```
President

Student ID: 10000001
Pawprint: aaaaaa
Password: president

Vice President

Student ID: 10000002
Pawprint: bbbbbb
Password: vicepresident

Recording Secretary

Student ID: 10000003
Pawprint: cccccc
Password: sec1

Corresponding Secretary

Student ID: 10000004
Pawprint: dddddd
Password: sec2

Treasurer

Student ID: 10000005
Pawprint: eeeeee
Password: treasurer

Recruitment Chair

Student ID: 10000006
Pawprint: ffffff
Password: recruitment

Initiate Advisor

Student ID: 10000007
Pawprint: gggggg
Password: initiate

Historian

Student ID: 10000008
Pawprint: hhhhhh
Password: historian

Parliamentarian

Student ID: 10000009
Pawprint: iiiiii
Password: parliament

Brother at Large

Student ID: 10000010
Pawprint: jjjjjj
Password: broatlarge

Fundraising Chair

Student ID: 10000011
Pawprint: kkkkkk
Password: funds

Alumni Chair

Student ID: 10000012
Pawprint: llllll
Password: alumni

Philanthropy Chair

Student ID: 10000013
Pawprint: mmmmmm
Password: philanthropy

Service Chair

Student ID: 10000014
Pawprint: nnnnnn
Password: service

DAS Chair

Student ID: 10000015
Pawprint: oooooo
Password: das

Fellowship Chair

Student ID: 10000016
Pawprint: pppppp
Password: fellowship

Risk Management Chair

Student ID: 10000017
Pawprint: qqqqqq
Password: risk

Rec Sports Chair (1)

Student ID: 10000018
Pawprint: rrrrrr
Password: rec1

Rec Sports Chair (2)

Student ID: 10000019
Pawprint: ssssss
Password: rec2

PR Chair (1)

Student ID: 10000020
Pawprint: tttttt
Password: pr1

PR Chair (2)

Student ID: 10000021
Pawprint: uuuuuu
Password: pr2

Campus Liaison

Student ID: 10000022
Pawprint: vvvvvv
Password: liaison

Regular Member

Student ID: 10000023
Pawprint: wwwwww
Password: member

```

## ERD:
> Must include an ERD in any type of notation (Chen, Crow’s Foot notation, or other variants). You should explain all constraints and provide DDL for all tables.

![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/DraftDatabaseERD%20(11)%20(3)%20(4).png "ERD")

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
> This query is necessary because everyone should be able to see their member info on the dashboard page as well as be able to update this information should something change.

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
> This query is necessary because each member should have full visibility as to how much they have owed and what left they have to pay.

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
> This query is useful to members who wish to see their payment plan and to members who hold chair positions.

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
> Each member needs to be able to see the amount of points they have because if they do not meet their quota, they will be charged a fine.

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
> This is important for each member to see in case they need to update their information. Before this application, new emergency contact info sheets were passed out each semester. Now, this information only needs to be resubmitted if there is a change in the data.

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
> Each member will also see the specific merch items as well as the details associated with each item on their dashboard. This is good to see because it reminds members how much they will owe for their merch.

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
> Each member should have the ability to see which teams they are apart of.

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
> Can be used to award the most active members.

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
> Useful for chair positions that need to contact all members via email, phone number, etc.

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
> Useful for members who wish to see how many points they have. Points are very important to being a Phi Sigma Pi member.

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
> This is a useful query because it is important to know who the leaders are in the fraternity as many times you need to reach out to them.

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
> This query is useful for the treasurer to see payment plans of all members.

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
> This is helpful for the organization when merch items are left over and you need to figure out who hasn’t completed their orders yet… no more searching through google docs!

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
> This is useful for members who want to see who is in their family.

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
> This is useful to chair positions who are in charge of planning events so they can gauge interest for future events as to what might be successful

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
> Each member with a chair position should have the ability to see their budget.

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
> Being able to see entire class rosters is important to the fraternity as a whole as many times you do separate things with just your class before joining the entire fraternity. Of course, everyone usually remembers everyone in their class after a while, but this can also be helpful data when reporting back to Nationals with new members.

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
> This query is useful for members with a chair position who run events and would like to see a list of their events for organizational purposes.

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
> This query is important because PSP loves to promote members who are constantly active and they do giveaways at the end of the semester to these individuals.

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
> All potential members is important for both reporting to nationals as well as helping the active brothers to get to know the potential members

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
> It is important to know which potential members have made it passed voting so far so that the chapter doesn’t vote on someone who is no longer being considered for initiation

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
> This analytic is especially important to the treasurer who can you this representation as a way to gauge the health of the budget in its current state

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
> This bar graph is important because it can be a general indicator of a problem if the bar for inactive slowly increases to be a substantial amount. It is also a good snapshot to send to Nationals.

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
> This bar graph is especially important to the chairs who are creating events as well as the chapter as a whole. The fraternity does not weight one kind of event as more important as another so there should be about an even number of events for each category. This can also be a good indication of a chair slacking off or their committee not helping out enough.

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
> This bar graph is helpful to the PR chairs who design and order merch. Many times, they only order the top 5 or so items they have designed because a large quantity needs to be ordered to drive the price and shipping down. It is also helpful to look at before making future designs.

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
> This chart provides an easy overview during voting periods to see the averages of the way people are voting. It can then be used to help draw the line at what percentage is needed for a potential to pass to the next round.

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

Our DBMS as a whole is in 3rd normal form. However, all tables minus the **member**, **chairPosition**, and **archive_chairPosition** are in BCNF. In terms of dependency preservation our project is dependency preserving because each table has their own set of dependencies and the only ones that transcend one table are preserved in relation tables and weak entities.

The member table as it is, is in 3NF because of the *pawprint*, *first_name*, and *last_name* attributes. Ideally these attributes would have a `UNIQUE(pawprint,first_name,last_name)` constraint but we decided along with our client that we should check for this constraint on the front-end and allow it to be edited/corrected but still create a new user/member in the system.

The **chairPostion** and **archive_chairPosition** tables are in 3NF because the *position_id* attribute of **archive_chairPosition** technically references the **chairPostion** table's *position_id* attribute without a foreign key constraint. The client wanted the **archive_chairPosition** table to stand alone from the **chairPostion** table, but we can still technically access the data such as *position_name* or *position_type* through a query even though this won't be used too often. In the future the client might decide to make this constraint or connection which shouldn't be too hard of a fix.

## Indexing:
> Give an in-depth discussion of indexing as it relates to your project
including how/why you created specific additional indexes. Provide the associated SQL statement for creation of those indexes.

> We chose to index on these tables because they were referenced a lot in our queries. If the use of our application expands to the use of other school chapters or for the user of Nationals, there would be much more data and these indexes would prove to be very efficient. Right now, with just a small set of data for just our chapter, it's not too big of a difference in efficiency but every millisecond counts!

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

The main area of optimization in our project was the ***Member* Table**. We originally had a big member table that encompassed the **member**, **member_status**, **member_address**, **member_contact**, and **emergencyContact** tables all in one big table. We realized this was pretty inefficient given that we could break down the member table into better distinct groupings. It didn't necessarily change the normal form of any of the tables but it made more logical sense to break up the member table.

As for future optimization, Phi Sigma Pi would like the ***Member* Table** to be horizontally decomposed into two tables. Both would be identical to the original ***Member* Table** but one would house **current members** and the other would track **alumni  members** and with a newly created view called **Member** that would be the union of the two new tables and function as the original. Horizontally decomposing the ***Member* Table** will make queries more efficient for both types of data once more members are added/graduate since the member table will essentially just keep growing as time progresses.

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

### Form Handling
+ Used pg_prepare and pg_execute to prevent SQL injection attacks and illegitimate queries
+ Checked event name with semester and year for creating events before running query to make sure wouldn't duplicate event names for the same semester and year

> Example statements for checking for duplicate event name for semester/year
+ $result1 = pg_prepare($db, "checkEventName", "SELECT * FROM events WHERE title = $1 AND date_part('year', event_date) = $2 AND semester = $3");
+ $result1 = pg_execute($db, "checkEventName", array($nameCheck, $year, $semester));

> Example statements for inserting into events table. The only difference between chair positions will be the first two arguments which reflect the position_id and point_type for the event being created
+ $result = pg_prepare($db, "createDAS", "INSERT INTO events (position_id, point_type, num_of_points, title, event_date, semester)
                    VALUES (15, 1, $1, $2, $3, $4)");
+ if($result){
          $result = pg_execute($db, "createDAS", array($point, $nameCheck, $date, $semester));
 }

## User’s manual:
> Provide a manual that allows anyone to learn how to operate every aspect of your project. Include screenshots of interfaces and step-by-step instructions. with interfaces and instructions to use your system.
![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/Screen%20Shot%202018-05-09%20at%201.43.43%20PM.png "FirstPic")
+ The first page that is presented when entering the website is the login page.
+ Login using the credentials provided.
+ After logging in you will be presented with the home page below.
![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/Screen%20Shot%202018-05-09%20at%201.46.01%20PM.png "FirstPic")
+ This page contains all the infomation regarding the user that is logged in.
+ Use the logout button to logout of the current user and to access the login page
+ Depending on what position the user has will allow or deny access to web pages that can be accesed with the top nav bar.
![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/Screen%20Shot%202018-05-09%20at%201.47.03%20PM.png "FirstPic")
+ This is the roster page. This page contains all members of the fraternity. It can be sorted by selecting Active, Locally Inactive, Nationally Inactive, and Alumni.
![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/Screen%20Shot%202018-05-09%20at%201.47.19%20PM.png "FirstPic")
+ This is the roster page after selecting Active and hitting the Display Roster button.
![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/Screen%20Shot%202018-05-09%20at%201.47.47%20PM.png "FirstPic")
+ This is the analytics page.
+ There are no actions on this page. It is for viewing analytics about PSP.

#### Creating Events

![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/CreateDASEvent.PNG "CreateDASEvent")

+ 5 Chair positions have the availability to add events worth their respective points. Those positions are DAS, Fellowship, Service, Fundraising, and Campus Liason. The chair must be signed in to access their tab for creating events. Log in by clicking the Member Login tab and entering in the proper credentials.
+ Once logged in, click on the create event tab for the respective position. For example, the DAS chair will select CreateDASEvent. 
+ Once here, a form pops up for the chair to fill out. All fields are required to fill out. 
+The event name can be whatever they want to name the event. However, a check will be made before exectuing the query to make sure that event name does not already exist for that semester and year that was selected. If it was found in that semester and year, a popup window will alert the user that the event name already exists for that year and semester and they will need to fill out the form again.
+ The amount of points the event is worth can be typed in or using the arrows on the right to increment or decrement. This field must be a number.
+ The date picker can be written in manually or selected using the date picker. 
+ Finally, the semester can be chosen as either Fall or Spring via the radio buttons with Fall as the default. 
+ Once the form has been filled out, the user can click submit to create the event and have it inserted into the database.

#### info

![alt text](https://github.com/HGinther/CS4830-FinalProject/blob/master/Pictures/info.PNG "Info Page")

+ Click on the info tab at the top to access info.php. 
+ Once at info.php, there is nothing to input. Simply scroll down to find the information you need. In the future, these queries will be split up among the proper chair positions and among the position types to show information to the proper individuals using mandatory access control
