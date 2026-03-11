# COW FARM MANAGEMENT SYSTEM

**PROJECT REPORT**  
Submitted to  
**DEPARTMENT OF COMPUTER SCIENCE**  
**(ARTIFICIAL INTELLIGENCE & DATA SCIENCE)**  
**GOBI ARTS & SCIENCE COLLEGE (AUTONOMOUS)**  
**GOBICHETTIPALAYAM-638453**

**By**  
**S. ESWARAN**  
**(23AI134)**

**Guided By**  
**Mr. K. MADHESWARAN, M.C.A., M.PHIL.**

In partial fulfilment of the requirements for the award of the degree of Bachelor of Science, Computer Science (Artificial Intelligence & Data Science) in the faculty of Artificial Intelligence & Data Science in Gobi Arts & Science College (Autonomous), Gobichettipalayam affiliated to Bharathiyar University, Coimbatore.

**MARCH – 2026**

---

## DECLARATION

I hereby declare that the project report entitled **“COW FARM MANAGEMENT SYSTEM"** submitted to the Principal, Gobi Arts & Science College (Autonomous), Gobichettypalayam, in partial fulfilment of the requirements for the award of degree of Bachelor of Science, Computer Science (Artificial Intelligence & Data Science) is a record of project work done by me during the period of study in this college under the supervision and guidance of **Mr. K. MADHESWARAN, M.C.A., M.Phil.** Assistant Professor of the Department of Artificial Intelligence & Data Science.

**Signature:**  
**Name:** S. ESWARAN  
**Register Number:** 23AI134  
**Date:**

---

## CERTIFICATES

This is to certify that the project report entitled **"COW FARM MANAGEMENT SYSTEM"** is a Bonafide work done by **S. ESWARAN (23AI134)** under my supervision and guidance.

**Signature of Guide:**  
**Name:** Mr. K. MADHESWARAN  
**Designation:** Assistant Professor  
**Department:** Computer Science (AI & DS)

**Counter Signed**

**Head of the Department** | **Principal**

**Viva-Voce held on:** ___________

**Internal Examiner** | **External Examiner**

---

## ACKNOWLEDGEMENT

The successful completion of this project titled **“COW FARM MANAGEMENT SYSTEM”** was not solely the result of my individual effort, but also the outcome of the guidance, encouragement and support received from many individuals. 

I extend my heartfelt thanks to the Management and College Council of **Gobi Arts & Science College (Autonomous)**, for providing the necessary facilities. I express my deep sense of gratitude to our respected Principal, **Dr. P. VENUGOPAL, M.Sc., M.Phil., PGDCA., Ph.D.**, and **Dr. M. RAMALINGAM**, Head of the Department of AI & DS.

I owe my deepest gratitude to my project guide, **Mr. K. MADHESWARAN**, for his constant supervision and constructive suggestions throughout the development of the system.

**S. ESWARAN**

---

## SYNOPSIS

The **“COW FARM MANAGEMENT SYSTEM”** is a high-performance web application designed to streamline the management of modern dairy farms. Managing livestock, health records, and milk production manually in large-scale farms often leads to data fragmentation and operational inefficiencies. This project provides a centralized digital solution to monitor animal health, track daily milk yield, manage breeding cycles, and oversee farm finances.

Developed using the **PHP-MySQL-Apache** stack (via XAMPP), the application ensures secure data storage and real-time accessibility within a local farm environment. The system features role-based access control, automated vaccination alerts, and detailed production reporting. By digitizing farm records, the system helps improve cattle health, optimizes milk production, and provides clear financial insights for better decision-making.

---

## CONTENTS

| CHAPTER | TITLE | PAGE NO. |
| :--- | :--- | :--- |
| | **ACKNOWLEDGEMENT** | **i** |
| | **SYNOPSIS** | **ii** |
| **1** | **INTRODUCTION** | **01** |
| | 1.1 ABOUT THE PROJECT | 01 |
| | 1.2 HARDWARE SPECIFICATIONS | 04 |
| | 1.3 SOFTWARE SPECIFICATIONS | 04 |
| **2** | **SYSTEM ANALYSIS** | **12** |
| | 2.1 PROBLEM DEFINITION | 12 |
| | 2.2 SYSTEM STUDY | 14 |
| | 2.3 PROPOSED SYSTEM | 16 |
| **3** | **SYSTEM DESIGN** | **19** |
| | 3.1 DATA FLOW DIAGRAM (DFD) | 19 |
| | 3.2 ENTITY RELATIONSHIP DIAGRAM | 20 |
| | 3.3 FILE SPECIFICATIONS | 21 |
| | 3.4 MODULE SPECIFICATIONS | 25 |
| **4** | **TESTING AND IMPLEMENTATION** | **28** |
| | 4.1 SYSTEM TESTING | 28 |
| | 4.2 IMPLEMENTATION | 29 |
| **5** | **CONCLUSION AND SUGGESTIONS** | **31** |
| | 5.1 CONCLUSION | 31 |
| | 5.2 SUGGESTIONS FOR FUTURE ENHANCEMENT | 31 |
| | **BIBLIOGRAPHY** | **32** |
| | **APPENDICES** | **33** |
| | APPENDIX – A (SCREEN FORMATS) | 33 |
| | APPENDIX – B (SOURCE CODE LISTINGS) | 37 |

---

## CHAPTER 1: INTRODUCTION

Web applications have become indispensable in modern agriculture, enabling farmers to transition from manual registers to automated data-driven systems. The **Cow Farm Management System** is a robust platform designed to manage the complexities of cattle farming with precision and ease.

### 1.1 ABOUT THE PROJECT

This project provides a digital ecosystem where farm owners, veterinarians, and staff can collaborate. It maintains comprehensive profiles for every cow, including their breed, lineage, medical history, and production statistics. The system acts as a central repository for all farm-related data, ensuring nothing is lost or overlooked.

**Objectives of the Project:**
- To digitize all cattle records and eliminate fragmented paper-based tracking.
- To improve herd health through automated vaccination and checkup reminders.
- To optimize milk production by analyzing individual and herd-level yield data.
- To manage farm finances by accurately tracking expenses and revenue.
- To facilitate effective breeding programs with pregnancy and calving tracking.

**Scope of the Project:**
The scope extends to the full lifecycle management of cattle on a farm. It includes authentication for different staff roles, detailed health logging, daily production tracking, inventory management for feed, and financial reporting. The system is designed for offline local server deployment, making it resilient to internet outages in rural farm settings.

### 1.2 HARDWARE SPECIFICATIONS

- **Processor:** Intel(R) Core (TM) i5-8500 @ 3.00 GHz
- **RAM:** 8 GB
- **Hard Disk:** 256 GB SSD
- **Monitor:** 19.5" Monitor
- **Keyboard/Mouse:** Standard USB peripherals

### 1.3 SOFTWARE SPECIFICATIONS

- **Operating System:** Windows 10/11
- **Web Server:** Apache (via XAMPP)
- **Backend Language:** PHP 7.4+
- **Database:** MySQL
- **Frontend Stack:** HTML5, CSS3, JavaScript
- **Frameworks/Libraries:** None (Vanilla implementation for maximum portability)

---

## CHAPTER 2: SYSTEM ANALYSIS

System analysis is used to understand the existing workflows and identify areas where a computerized system can provide significant improvements.

### 2.1 PROBLEM DEFINITION

Manual farm management faces several critical bottlenecks that hinder the scalability and profitability of dairy operations:
- **Data Inaccuracy and Human Error:** Hand-written logs are often illegible or subject to transcription errors. Misreading a single digit in a medication dosage or a milk yield count can lead to significant financial loss or cattle health risks.
- **Lack of Real-time Alerting:** Without an automated system, critical lifecycle events such as vaccination windows, deworming cycles, and pregnancy checkups are managed via static calendars. The risk of missing these windows is high, leading to herd-wide health vulnerabilities.
- **Inefficient Financial Reporting:** Calculating the return on investment (ROI) for individual cows or the entire herd manually takes hours of data aggregation. This manual process is prone to errors, making it difficult for farmers to identify underperforming assets or optimize feed costs.
- **Traceability and Lineage Ambiguity:** In modern farming, tracking the genetic lineage of cattle is essential for breeding programs. Traditional registers struggle to provide a clean, multi-generational view of a cow's ancestry, making selective breeding a guessing game.

### 2.2 SYSTEM STUDY

**Existing System:** Most farms still rely on physical notebooks kept in the barn environment. These registers are often exposed to moisture, dust, and physical damage. Searching through years of paper logs to find a specific cow's history is nearly impossible, and generating a 12-month production trend requires weeks of manual effort.

**Proposed System:** The new system introduces a searchable, relational digital database. Every animal is assigned a unique biometric or tag ID that acts as the primary key for all related data points. From birth to sale, every event—medication, yield, breeding, and weight gain—is timestamped and indexed. This ensures 100% data integrity and allows for near-instantaneous reporting and trend analysis.

### 2.3 PROPOSED SYSTEM (FEASIBILITY STUDY)

- **Technical Feasibility:** The project leverages the robust PHP-MySQL stack, which is industry-proven for mid-scale data applications. The system's architecture supports rapid indexing and complex queries even on low-power hardware typically found in farm offices.
- **Economic Feasibility:** By using the open-source XAMPP stack, the project avoids recurring software licensing fees. The primary investment is in standard PC hardware, which is offset within months by the reduction in labor hours and improved cattle health outcomes.
- **Operational Feasibility:** The interface utilizes "progressive disclosure" design principles, showing only necessary information to barn staff while keeping advanced reporting for management. This ensures a low learning curve for non-technical users.

---

## CHAPTER 3: SYSTEM DESIGN

### 3.1 DATA FLOW DIAGRAM (DFD)

The system architecture follows a synchronized data flow designed for the unique demands of a farm environment:
1. **Data Acquisition:** Staff enter raw data (milk weights, feed consumption, vet observations) via web forms.
2. **Business Logic Layer:** The PHP backend executes validation scripts to ensure data consistency (e.g., checking if a cow is actually in the 'active' state before logging milk).
3. **Data Persistence:** Records are committed to MySQL using atomic transactions to prevent data corruption during power outages.
4. **Information Delivery:** The system pulls aggregated data to generate real-time KPIs, historical charts, and printable PDF reports for financial audits.

### 3.2 ENTITY RELATIONSHIP DIAGRAM

The database consists of specialized tables for `users`, `cows`, `health_records`, `vaccinations`, `milk_production`, and `breeding_records`. `cows` is the central hub, linked via `cow_id` to all activity logs.

### 3.3 FILE SPECIFICATIONS

#### Table Name: `users`
**Purpose:** Stores user accounts for authentication and role-based access.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique identifier for the user |
| username | VARCHAR | 50 | Unique, Not Null | Login username |
| email | VARCHAR | 100 | Unique, Not Null | Email address |
| password | VARCHAR | 255 | Not Null | Hashed password |
| full_name | VARCHAR | 100 | Not Null | User's full name |
| role | ENUM | - | Default 'staff' | User role (admin/vet/manager/staff) |
| phone | VARCHAR | 20 | - | Contact number |
| status | ENUM | - | Default 'active' | Account status |
| created_at | TIMESTAMP | - | Default Current | Record creation time |

#### Table Name: `cows`
**Purpose:** Stores comprehensive profiles and current status of all cattle in the farm.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique identifier for the cow |
| tag_number | VARCHAR | 50 | Unique, Not Null | Unique identification tag number |
| name | VARCHAR | 100 | - | Name of the cow |
| breed | VARCHAR | 50 | - | Breed name |
| date_of_birth | DATE | - | - | Birth date of the cow |
| gender | ENUM | - | Not Null | Gender (male/female) |
| weight | DECIMAL | 8,2 | - | Weight in kg |
| color | VARCHAR | 50 | - | Color of the cow |
| status | ENUM | - | Default 'active' | Current status (active/sold/deceased) |
| created_by | INT | - | Foreign Key | ID of the user who created the record |
| created_at | TIMESTAMP | - | Default Current | Record creation time |

#### Table Name: `health_records`
**Purpose:** Maintains medical history, checkups, and treatments for each cow.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| cow_id | INT | - | Foreign Key, Not Null | Reference to cows table |
| record_date | DATE | - | Not Null | Date of health record |
| record_type | ENUM | - | Not Null | Type (checkup, treatment, surgery, etc.) |
| diagnosis | TEXT | - | - | Medical diagnosis details |
| medication | VARCHAR | 255 | - | Medication prescribed |
| dosage | VARCHAR | 100 | - | Dosage of medication |
| cost | DECIMAL | 10,2 | - | Cost of treatment |
| created_by | INT | - | Foreign Key | User who logged the record |

#### Table Name: `vaccinations`
**Purpose:** Tracks vaccination schedules and completion for the herd.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| cow_id | INT | - | Foreign Key, Not Null | Reference to cows table |
| vaccine_name | VARCHAR | 100 | Not Null | Name of the vaccine |
| vaccination_date | DATE | - | Not Null | Date administered |
| next_due_date | DATE | - | - | Due date for the next dose |
| administered_by | VARCHAR | 100 | - | Person who administered |
| cost | DECIMAL | 10,2 | - | Cost of vaccination |

#### Table Name: `breeding_records`
**Purpose:** Manages the lifecycle events related to cattle reproduction.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| cow_id | INT | - | Foreign Key, Not Null | Reference to cows table |
| breeding_type | ENUM | - | Not Null | Type (AI, natural, embryo) |
| breeding_date | DATE | - | Not Null | Date of insemination/breeding |
| bull_tag | VARCHAR | 50 | - | Tag number of the sire |
| expected_calving_date | DATE | - | - | Calculated expected delivery date |
| pregnancy_status | ENUM | - | Default 'pregnant' | Status (pregnant, aborted, delivered) |

#### Table Name: `milk_production`
**Purpose:** Records daily milk yields to analyze productivity.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| cow_id | INT | - | Foreign Key, Not Null | Reference to cows table |
| production_date | DATE | - | Not Null | Date of milk collection |
| session | ENUM | - | Default 'both' | Milking session (morning/evening) |
| morning_yield | DECIMAL | 8,2 | - | Morning yield in liters |
| evening_yield | DECIMAL | 8,2 | - | Evening yield in liters |
| total_yield | DECIMAL | 8,2 | - | Daily total yield |
| recorded_by | INT | - | Foreign Key | User who logged the record |

#### Table Name: `feed_inventory`
**Purpose:** Monitors the stock levels of different cattle feeds.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| feed_type_id | INT | - | Foreign Key, Not Null | Reference to feed types |
| quantity | DECIMAL | 10,2 | Not Null | Current stock quantity |
| unit_price | DECIMAL | 10,2 | - | Price per unit |
| purchase_date | DATE | - | - | Date of purchase |
| status | ENUM | - | Default 'available' | Stock status (available, low_stock, etc.) |

#### Table Name: `expenses`
**Purpose:** Tracks all financial expenditures related to farm operations.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| expense_date | DATE | - | Not Null | Date the expense occurred |
| category | ENUM | - | Not Null | Expense type (feed, medicine, etc.) |
| description | VARCHAR | 255 | Not Null | Short description of the expense |
| amount | DECIMAL | 10,2 | Not Null | Total expense amount |
| payment_method| ENUM | - | Default 'cash' | Method of payment |
| created_by | INT | - | Foreign Key | User who logged the record |

#### Table Name: `sales`
**Purpose:** Records milk or livestock sales to track revenue.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| sale_date | DATE | - | Not Null | Date the sale occurred |
| customer_name | VARCHAR | 100 | - | Name of the buyer/depot |
| milk_quantity | DECIMAL | 8,2 | Not Null | Total liters sold |
| total_amount | DECIMAL | 10,2 | Not Null | Revenue generated |
| payment_status | ENUM | - | Default 'paid' | Status (paid, pending, partial) |

#### Table Name: `appointments`
**Purpose:** Schedules vet visits and important farm activities.
| Field Name | Data Type | Size | Constraints | Description |
| :--- | :--- | :--- | :--- | :--- |
| id | INT | - | Primary Key, Auto-increment | Unique record ID |
| appointment_date | DATE | - | Not Null | Date of the appointment |
| appointment_time | TIME | - | - | Time of the appointment |
| cow_id | INT | - | Foreign Key | Reference to specific cow (optional) |
| vet_name | VARCHAR | 100 | - | Name of the visiting vet |
| status | ENUM | - | Default 'scheduled'| Appointment status (scheduled, completed) |

### 3.4 MODULE SPECIFICATIONS

The Cow Farm Management System is designed as a modular web application that consists of several functional modules. Each module performs a specific operation in the system and works together to provide an efficient platform for modern dairy farm management. The modular structure makes the system easier to develop, test, and maintain. It also allows developers to update or modify one module without affecting the other modules of the system.

**List of Modules:**
• Authentication & User Management
• Cow Profile & Herd Management
• Health & Vaccination Management
• Milk Production Management
• Breeding & Pregnancy Tracking
• Feed & Inventory Management
• Financial Management
• Admin & Reporting

**AUTHENTICATION & USER MANAGEMENT**
The Authentication & User Management module is responsible for handling user access to the system. This module allows farm administrators to register staff and veterinarians, and users to log in securely using their credentials. It verifies the credentials entered by the user and allows access only if the information is correct, helping to maintain data security and privacy.

The module also manages user profiles by storing basic information such as name, email, and role assignments (Admin, Manager, Vet, Staff). It maintains session control and ensures that based on the user's role, they are granted appropriate access privileges to specific functional areas of the application.

**COW PROFILE & HERD MANAGEMENT**
The Cow Profile & Herd Management module is used to organize and manage the cattle in the farm. This module allows administrators and staff to create detailed profiles for each cow, capturing essential information like breed, date of birth, tag number, gender, genetics, and current status.

Users can browse the herd, search using tag numbers, and select individual cows to view their comprehensive history in a centralized view. It allows staff to update or remove profiles whenever necessary, tracking their lifecycle from active status to sold or deceased. This module plays an important role in providing a structured baseline for all other farm activities.

**HEALTH & VACCINATION MANAGEMENT**
The Health & Vaccination Management module allows veterinarians and farm managers to track the well-being of the herd. Through this module, users can log daily checkups, diagnose illnesses, and record treatments or medications given to specific cows.

The module also tracks vaccination schedules, alerting the farm management about upcoming or overdue vaccinations. By maintaining a well-organized medical history, the system ensures that the herd remains healthy, reducing the risk of disease spread within the farm and preventing production losses.

**MILK PRODUCTION MANAGEMENT**
The Milk Production Management module controls the process of recording daily milk yields. Staff can input the milk quantity collected from each cow during morning and evening milking sessions. The system automatically calculates total daily yields and evaluates the performance of individual animals.

By systematically storing this data, this module ensures that production levels are monitored consistently. It generates performance trends that help farmers identify high-yielding cows, detect sudden drops in output, and make informed feeding or management decisions.

**BREEDING & PREGNANCY TRACKING**
This module manages the lifecycle events related to cattle reproduction. Administrators and veterinarians can configure and record breeding data, including artificial insemination dates, expected calving dates, and pregnancy status. 

This tracking ensures proper monitoring of the reproductive health of the herd. It provides automated timelines for the gestational period and helps farmers in forecasting the future herd size and planning the transition of cows from the milking herd to the dry cow group.

**FEED & INVENTORY MANAGEMENT**
The Feed & Inventory Management module is designed to monitor the stock levels of structural cattle feed and dietary supplements. It allows the farm manager to log incoming feed purchases, track unit prices, and monitor overall stock levels.

The system provides low-stock alerts and statuses, ensuring that the farm never runs out of essential ingredients. By standardizing feed tracking against inventory limits, the farm can optimize feed purchasing strategies and avoid emergency procurement costs.

**FINANCIAL MANAGEMENT**
The Financial Management module tracks the cash flow associated with running the dairy farm. It records all daily expenses such as feed purchases, medical costs, labor wages, and maintenance fees to keep track of the farm's outgoings.

On the revenue side, it tracks milk sales to various customers and local depots, generating clear insights on income. By comparing aggregated revenue against operational expenses, this module helps farm owners assess profitability and cash flow.

**ADMIN & REPORTING**
The Admin & Reporting module provides administrative control for managing the entire system. Administrators can manage system settings, review logs, and oversee all farm operations through an interactive dashboard.

The reporting feature generates aggregated reports based on milk production, health statistics, feed inventory, and financial records. These visual charts and tabular analyses help administrators evaluate the performance of the farm and understand operational bottlenecks, facilitating data-driven decisions that maximize efficiency.

---

## CHAPTER 4: TESTING AND IMPLEMENTATION

### 4.1 SYSTEM TESTING

- **Unit Testing:** Validated individual database connection strings and helper functions.
- **Integration Testing:** Ensured that milk production logs correctly update the dashboard's total yield count.
- **Security Testing:** Verified that `vet` users cannot delete financial records and `staff` cannot access management pages.
- **Validation Testing:** Checked that only positive numbers are accepted for milk yield and expense amounts.

### 4.2 IMPLEMENTATION

The implementation involved setting up the XAMPP environment, importing the `schema.sql` into phpMyAdmin, and configuring the base URL in the project files. The system was tested with 20 sample cow records representing traditional Tamil Nadu breeds (Kangayam, Gir) to ensure realistic performance.

---

## CHAPTER 5: CONCLUSION AND SUGGESTIONS

### 5.1 CONCLUSION

The **Cow Farm Management System** successfully transitions traditional farm record-keeping into the digital age. It provides a reliable framework for herd management, production tracking, and financial oversight. The system's ability to operate offline makes it a practical solution for modern farmers.

### 5.2 SUGGESTIONS FOR FUTURE ENHANCEMENT

1. **IoT Integration:** Smart collars for automated health and activity monitoring.
2. **Mobile App:** A native android application for onsite barn data entry.
3. **Cloud Backup:** Optional synchronization for data safety across multiple farm locations.
4. **Predictive Analytics:** AI-driven yield forecasting based on historical health and feed data.

---

## BIBLIOGRAPHY

### Book References

1. **Robin Nixon** (2021). *Learning PHP, MySQL & JavaScript: With jQuery, CSS & HTML5* (6th Edition). O'Reilly Media.
2. **Luke Welling, Laura Thomson** (2016). *PHP and MySQL Web Development* (5th Edition). Addison-Wesley Professional.
3. **Jon Duckett** (2011). *HTML and CSS: Design and Build Websites*. Wiley.
4. **Jon Duckett** (2014). *JavaScript and JQuery: Interactive Front-End Web Development*. Wiley.
5. **Joel Murach, Ray Harris** (2017). *Murach's PHP and MySQL* (3rd Edition). Mike Murach & Associates.

### URL References

1. **Official PHP Manual** - Comprehensive guide on PDO and Prepared Statements.  
   *URL:* [https://www.php.net/manual/en/](https://www.php.net/manual/en/)
2. **MySQL Reference Manual** - Guidelines for relational database design and optimization.  
   *URL:* [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)
3. **W3Schools Online Web Tutorials** - Standard HTML5, CSS3, and JavaScript implementation patterns.  
   *URL:* [https://www.w3schools.com/](https://www.w3schools.com/)
4. **Apache Friends (XAMPP Project)** - Cross-platform local server environment configuration guides.  
   *URL:* [https://www.apachefriends.org/](https://www.apachefriends.org/)
5. **MDN Web Docs** - Mozilla's official developer network for comprehensive web technology documentation.  
   *URL:* [https://developer.mozilla.org/](https://developer.mozilla.org/)

---

## APPENDICES

### APPENDIX – A (SCREEN FORMATS)

#### A1. Main Dashboard
![Dashboard Viewport](screenshort/viewport/dashboard_viewport.png)
*The central hub providing real-time analytics on herd count, milk yield, and financial status.*

#### A2. Cow Profiles List
![Cow List Viewport](screenshort/viewport/cows_list_viewport.png)
*A comprehensive registry of the entire herd with search, filter, and quick action capabilities.*

#### A3. Health records
![Health Records](screenshort/viewport/health_records_viewport.png)
*Detailed medical history tracking for individual cows, including diagnosis and treatment logs.*

#### A4. Vaccination Schedule
![Vaccinations](screenshort/viewport/vaccinations_viewport.png)
*Dynamic calendar and list view for managing upcoming and completed livestock vaccinations.*

#### A5. Breeding & Pregnancy
![Breeding](screenshort/viewport/breeding_viewport.png)
*Specialized tracking for insemination dates, pregnancy status, and expected calving dates.*

#### A6. Milk Production Log
![Milk Production](screenshort/viewport/milk_production_viewport.png)
*Daily recording interface for morning and evening milk yields per cow.*

#### A7. Feed & Inventory
![Feed Inventory](screenshort/viewport/feed_inventory_viewport.png)
*Stock management for cattle feed and supplements with automated low-stock alerts.*

#### A8. User Management
![Users](screenshort/viewport/users_viewport.png)
*Role-based access control interface for managing system administrators and farm staff.*

#### A9. Financial Expenses
![Expenses](screenshort/viewport/expenses_viewport.png)
*Expense tracking module for feed purchases, medical costs, and general farm utility bills.*

#### A10. Vet Appointments
![Appointments](screenshort/viewport/appointments_viewport.png)
*Scheduling system for routine veterinarian checkups and emergency medical visits.*

#### A11. System Alerts
![Alerts](screenshort/viewport/alerts_viewport.png)
*Centralized notification center for urgent health alerts and inventory reminders.*

#### A12. System Settings
![Settings](screenshort/viewport/settings_viewport.png)
*Configuration panel for farm details, system preferences, and backup management.*

### APPENDIX – B (REPORT FORMATS)

#### B1. Analytical Reports Module
![Reports Module](screenshort/viewport/reports_viewport.png)
*The main reporting engine capable of generating custom date-range performance summaries.*

#### B2. Full Dashboard Analytics
![Dashboard Full](screenshort/fullheight/dashboard_fullheight.png)
*Comprehensive vertical dashboard view showing all data cards and performance charts.*

#### B3. Complete Herd Overview
![Cow List Full](screenshort/fullheight/cows_list_fullheight.png)
*Full-length herd registry view optimized for printing and high-level auditing.*

#### B4. Milk Production Summary
![Milk Production Full](screenshort/fullheight/milk_production_full.png)
*Detailed production report showing historical totals and individual cow performance.*

#### B5. Vaccination Due List
![Vaccinations Full](screenshort/fullheight/vaccinations_full.png)
*Automated report generating a list of all livestock requiring medical attention in the next 30 days.*

### APPENDIX – C (COMPLETE SOURCE CODE LISTINGS)

- **C1. `config/database.php`**: Primary connection class.
- **C2. `classes/Auth.php`**: Secure RBAC logic.
- **C3. `cows/add.php`**: Cow profile registration logic.
- **C4. `milk/index.php`**: Yield tracking system.
- **C5. `database/schema.sql`**: Full database structure.


---
