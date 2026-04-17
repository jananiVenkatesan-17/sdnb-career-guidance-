-- ============================================================
-- PLACIFY — placement_faq TABLE
-- Covers: CS, Commerce, Arts, Basic Sciences, Management, Vocational
-- ============================================================

CREATE TABLE IF NOT EXISTS `placement_faq` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `dept`       VARCHAR(30) NOT NULL COMMENT 'cs | commerce | arts | basic_science | management | vocational | general',
  `category`   VARCHAR(30) NOT NULL COMMENT 'general | eligibility | skills | training | interview',
  `question`   TEXT        NOT NULL,
  `answer`     TEXT        NOT NULL,
  `keywords`   TEXT        NULL     COMMENT 'comma-separated match keywords'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE `placement_faq`;

INSERT INTO `placement_faq` (`dept`,`category`,`question`,`answer`,`keywords`) VALUES

-- ============================================================
-- COMPUTER SCIENCE / TECH DEPT
-- ============================================================

-- General Placement
('cs','general','What type of companies visit for Computer Science students?',
 '🏢 IT companies, software firms, and multinational companies visit for CS/BCA/MCA students.\n✅ Examples: TCS, Infosys, Wipro, Cognizant, Zoho, Accenture, HCL, Capgemini, Tech Mahindra, Freshworks.',
 'companies,visit,which company,who recruit,company list,it company,software company,mnc,cs company,bca company,mca company'),

('cs','general','What is the average salary for Computer Science students?',
 '💰 The average salary for CS/BCA/MCA students is ₹3–₹6 LPA depending on the company and role.\n⭐ Top performers can receive ₹8–₹10 LPA from product companies like Zoho and Freshworks.',
 'average salary,salary,package,lpa,pay,ctc,stipend,how much salary,salary range,cs salary,bca salary,mca salary'),

('cs','general','What job roles are offered for Computer Science students?',
 '💼 Common roles include:\n• Software Developer / Engineer\n• Programmer Analyst\n• Data Analyst\n• QA / Tester\n• IT Support Engineer\n• Web Developer\n• System Engineer',
 'job role,role,position,designation,what job,what work,job offer,job title,software job,it job'),

('cs','general','When do placements start for CS students?',
 '📅 Placement drives usually begin in the final year (6th semester for UG, 4th semester for PG).\nPre-placement preparations start from 2nd year itself.',
 'when placement,placement start,when drive,drive begin,placement time,final year placement,when interview'),

('cs','general','Are internships provided before placement?',
 '✅ Yes! The college actively supports internships from the 2nd year onwards.\nInternship experience greatly improves your placement chances and resume quality.',
 'internship,intern,internship before,provide internship,internship support,internship help'),

('cs','general','Is placement guaranteed for all students?',
 '📌 Placement is an opportunity, not a guarantee. All eligible students are given opportunities to attend drives.\nFinal selection depends on your skills, performance, and attitude during interviews.',
 'guarantee,guaranteed,placement guarantee,all get,everyone get,assurance,sure placement'),

('cs','general','Can students apply off-campus?',
 '✅ Yes! Students can apply off-campus through LinkedIn, Naukri, Indeed, company websites, and employee referrals.\nOff-campus drives often have more openings than on-campus ones.',
 'off campus,off-campus,apply outside,outside campus,naukri,linkedin,indeed,job portal'),

('cs','general','Are startup opportunities available for CS students?',
 '🚀 Yes! Many CS students join startups as developers, data analysts, or product engineers.\nStartups often pay ₹4–₹8 LPA with equity options and fast career growth.',
 'startup,start up,startup job,startup opportunity,small company,new company'),

-- Eligibility
('cs','eligibility','What is the minimum CGPA required for CS placements?',
 '📊 Most IT companies require a minimum CGPA of 6.0 or above.\nTop companies like TCS, Infosys, and Wipro also check school marks (10th & 12th ≥ 60%).',
 'cgpa,minimum cgpa,percentage,marks required,cut off,cutoff,gpa,score required,academic requirement'),

('cs','eligibility','Are arrears allowed for CS placements?',
 '⚠️ Most IT companies strictly do not allow active arrears at the time of interview.\nStudents should clear all backlogs before final year to be eligible for top companies.',
 'arrear,arrears,backlog,history of arrear,pending subject,failed subject,kt'),

('cs','eligibility','Is attendance important for placement eligibility?',
 '✅ Yes, attendance is important. Most colleges require 75%+ attendance to be eligible for placement drives.\nAbsence also affects your learning, which impacts interview performance.',
 'attendance,attendance required,attendance important,percentage attendance,75%,minimum attendance'),

('cs','eligibility','Do companies check 10th and 12th marks?',
 '📋 Yes, many companies like TCS, Infosys, and Wipro check school marks.\nGeneral requirement: 60% or above in 10th, 12th, and degree.\nMaintain consistent academic performance throughout.',
 '10th,12th,school marks,sslc,hsc,higher secondary,school percentage,board marks'),

('cs','eligibility','Can students with a gap year apply for placements?',
 '📌 Some companies allow a 1-year gap with a valid reason (health, family).\nBe prepared to explain your gap clearly and positively during interviews.\nSkill certifications during the gap year strengthen your profile.',
 'gap year,year gap,gap,career gap,year off,took a break,study gap'),

-- Skills
('cs','skills','What technical skills are required for CS placements?',
 '🔬 Core technical skills needed:\n• Programming: Java, Python, or C++\n• Data Structures & Algorithms (DSA)\n• DBMS and SQL\n• Operating Systems concepts\n• Computer Networks basics\n• Object-Oriented Programming\n• Git & version control\n• HTML/CSS basics (for web roles)',
 'technical skill,tech skill,what skill,skill required,which skill,important skill,hard skill,core skill,cs skill,it skill'),

('cs','skills','Which programming languages should I learn?',
 '💻 For placement, focus on:\n• Java (most companies ask this)\n• Python (data/AI roles)\n• C or C++ (for logic-based rounds)\n• SQL (for database roles)\n• JavaScript (for web developer roles)\n💡 Master at least ONE language deeply before spreading to others.',
 'programming language,language,java,python,c language,which language,coding language,learn language,best language'),

('cs','skills','Is coding important for placement?',
 '✅ Yes! Coding is the most critical skill for CS placements.\n📌 Practice daily:\n• LeetCode — for DSA\n• HackerRank — for skill tests\n• GeeksforGeeks — for concepts\nMost companies have 1–2 coding rounds before the interview.',
 'coding,code,coding important,is coding,programming,dsa,algorithm,leetcode,hackerrank'),

('cs','skills','Are projects important for CS placements?',
 '✅ Projects are very important! Recruiters always ask about projects.\n📌 Build at least 2–3 projects:\n• One using your core language (Java/Python)\n• One with a database (MySQL)\n• One full-stack or AI/ML project (if applicable)\nHost your projects on GitHub.',
 'project,projects,mini project,project important,build project,portfolio,github,project work'),

('cs','skills','Are certifications useful for CS students?',
 '✅ Yes! Certifications boost your profile and show initiative.\n📌 Recommended certifications:\n• AWS Cloud Practitioner (free tier)\n• Google Data Analytics (Coursera)\n• NPTEL Programming certifications\n• HackerRank skill badges (free)\n• TCS iON certifications',
 'certification,certify,certificate,course certificate,online course,nptel,coursera,udemy,certification useful'),

-- Training
('cs','training','Does the college provide placement training for CS students?',
 '✅ Yes! The college provides structured placement training including:\n• Aptitude & reasoning classes\n• Technical interview preparation\n• Coding workshops\n• Mock interviews\n• Resume building sessions\n• Industry expert guest lectures',
 'training,placement training,college training,prepare,preparation,training provided,what training,coaching'),

('cs','training','What training is given in the first year?',
 '📚 First Year Training:\n• Communication & English improvement\n• Basic programming fundamentals\n• Personality development\n• Introduction to placement process',
 'first year training,year 1,1st year,freshman training,first year prepare'),

('cs','training','What training is given in the second year?',
 '📚 Second Year Training:\n• Aptitude (Quantitative, Logical, Verbal)\n• Core technical subjects (DSA, DBMS, OOP)\n• Internship guidance\n• Resume writing basics',
 'second year training,year 2,2nd year,sophomore training,second year prepare'),

('cs','training','What training is given in the final year?',
 '📚 Final Year Training:\n• Full mock interviews (technical + HR)\n• Coding practice sessions\n• Company-specific preparation\n• Group discussion practice\n• Resume review and finalizing',
 'final year training,year 3,3rd year,last year training,final year prepare,6th sem,4th sem'),

('cs','training','Are mock interviews conducted?',
 '✅ Yes! Mock interviews are conducted regularly in the final year.\nThey simulate real interview conditions with technical and HR rounds.\n💡 Take every mock interview seriously — it builds confidence and reveals your weak areas.',
 'mock interview,mock,practice interview,simulate interview,rehearse interview,trial interview'),

('cs','training','Are aptitude classes conducted?',
 '✅ Yes, aptitude classes are conducted from the second year.\nTopics covered: Quantitative Aptitude, Logical Reasoning, Verbal Ability.\n📌 Practice daily on IndiaBix and PrepInsta in addition to class sessions.',
 'aptitude class,aptitude training,quant class,reasoning class,aptitude conducted,aptitude session'),

-- Interview
('cs','interview','What should be included in a CS resume?',
 '📄 A strong CS resume must include:\n1. Name, Email, Phone, LinkedIn, GitHub\n2. Professional Objective (2–3 lines)\n3. Education (college, school with %)\n4. Technical Skills (languages, tools)\n5. Projects (2–3 with tech stack)\n6. Certifications\n7. Internships (if any)\n8. Extra-curriculars\n💡 Keep it to 1 page. No photos. Use Arial/Calibri font.',
 'resume,cv,what resume,resume include,resume content,resume format,resume tips,build resume,resume sections'),

('cs','interview','What questions are asked in technical interviews?',
 '💻 Common technical interview questions for CS:\n• Explain OOP concepts (inheritance, polymorphism)\n• What is a linked list / stack / queue?\n• Write a program to reverse a string\n• Explain normalization in DBMS\n• What is time complexity?\n• Describe a project you built\n• SQL queries (SELECT, JOIN, GROUP BY)\n💡 Practice explaining your code out loud!',
 'technical interview question,tech question,what questions,interview question,technical round,what asked,coding question'),

('cs','interview','What is an HR interview for CS students?',
 '🤝 HR Interview covers:\n• Tell me about yourself\n• Why should we hire you?\n• What are your strengths and weaknesses?\n• Where do you see yourself in 5 years?\n• Why do you want to join this company?\n• How do you handle pressure?\n💡 Use the STAR method (Situation, Task, Action, Result) for behavioral questions.',
 'hr interview,hr round,hr question,human resource interview,behavioral interview,personality round'),

('cs','interview','How should I answer if I don\'t know an answer in interview?',
 '💡 Be honest! Say:\n"I\'m not sure about this right now, but based on my understanding of [related concept], I believe it works like..."\nor simply:\n"I don\'t know this currently, but I\'m eager to learn it."\n✅ Honesty is appreciated. Trying to bluff is the worst thing you can do.',
 'don\'t know answer,not know,no answer,blank,forgot,unknown question,what if i don\'t know,don\'t know'),

('cs','interview','Is confidence important in interviews?',
 '✅ Confidence is extremely important! Recruiters look for:\n• Eye contact\n• Clear voice\n• Positive body language\n• Composure under pressure\n💡 Practice mock interviews, record yourself, and work on your delivery every day.',
 'confidence,confident,nervousness,nervous,fear,shy,anxiety,stage fear,interview confidence'),

('cs','interview','Should I ask questions at the end of the interview?',
 '✅ Yes! Always ask 1–2 relevant questions. Examples:\n• "What does a typical day look like for this role?"\n• "What skills would help me succeed here?"\n• "What is the next step in the process?"\nAsking questions shows you are genuinely interested and prepared.',
 'ask question,question at end,ask interviewer,questions to ask,end of interview,should i ask'),

-- ============================================================
-- COMMERCE DEPT
-- ============================================================

('commerce','general','What type of companies visit for Commerce students?',
 '🏢 Companies from banking, finance, auditing, insurance, and corporate sectors visit for Commerce students.\n✅ Examples: HDFC Bank, ICICI Bank, Deloitte, KPMG, EY, PwC, Axis Bank, Bajaj Finserv, Amazon Finance.',
 'companies,company,commerce company,which company,bank,finance company,who recruit,mnc commerce,bcom company,bba company'),

('commerce','general','What is the average salary for Commerce students?',
 '💰 Average salary: ₹2–₹4 LPA depending on the company and role.\n⭐ Highest salary: ₹6–₹7.5 LPA in audit/consulting firms (Deloitte, EY, KPMG).\nBanking roles typically start at ₹3–₹3.5 LPA.',
 'salary,package,lpa,pay,ctc,average salary,commerce salary,bcom salary,bba salary,how much,income'),

('commerce','general','What job roles are offered for Commerce students?',
 '💼 Common roles for Commerce students:\n• Accountant / Financial Analyst\n• HR Executive\n• Banking Associate / Relationship Manager\n• Auditor / Audit Associate\n• Business Analyst\n• Marketing Executive\n• Operations Executive\n• Tax Consultant',
 'job role,role,position,designation,what job,commerce job,bcom job,bba job,job offered,work profile'),

('commerce','general','When do placements start for Commerce students?',
 '📅 Placement drives usually begin during the final year.\nPre-placement training and mock interviews start from 2nd year.\n💡 Prepare your resume and skills from 2nd year itself!',
 'when placement,placement start,when drive,drive begin,placement time,final year,when interview,placement date'),

('commerce','eligibility','What is the minimum CGPA for Commerce placements?',
 '📊 Most companies require CGPA 6.0 or above.\nTop firms like Deloitte and EY may look for 7.0+.\nSchool marks (10th and 12th) are also checked — aim for 60%+.',
 'cgpa,minimum cgpa,percentage,marks required,cutoff,gpa,score required,eligibility marks'),

('commerce','eligibility','Are arrears allowed for Commerce placements?',
 '⚠️ Most companies do not allow students with active arrears.\nClear all pending subjects before your final year to stay eligible.\nSome BPO/KPO companies may be flexible — check individual company policies.',
 'arrear,arrears,backlog,history of arrear,pending subject,failed subject,kt,standing arrear'),

('commerce','skills','What technical skills are required for Commerce students?',
 '🔬 Key technical skills for Commerce placements:\n• MS Excel (VLOOKUP, Pivot Tables, formulas)\n• Tally / Tally Prime\n• GST & Taxation basics\n• Accounting & Bookkeeping\n• SAP Basics (for corporate roles)\n• MS Office (Word, PowerPoint)\n• Basic data analysis',
 'technical skill,skill required,commerce skill,what skill,important skill,bcom skill,bba skill,ms excel,tally,gst,accounting'),

('commerce','skills','Is MS Excel important for Commerce placements?',
 '✅ Yes! MS Excel is one of the most important tools for Commerce roles.\n📌 Learn these Excel skills:\n• VLOOKUP / HLOOKUP\n• Pivot Tables\n• IF, SUMIF, COUNTIF formulas\n• Charts and data visualization\n• Basic Macros (VBA)\nMany interview tests include an Excel practical round.',
 'excel,ms excel,spreadsheet,excel important,excel useful,excel skill,microsoft excel'),

('commerce','skills','Is GST knowledge important?',
 '✅ Yes! GST knowledge is essential for accounting and finance roles.\n📌 Key GST topics to know:\n• GST registration types (CGST, SGST, IGST)\n• Filing returns (GSTR-1, GSTR-3B)\n• Input Tax Credit\n• GST calculation\nGet a GST certification from a CA institute or online platform.',
 'gst,goods service tax,taxation,tax knowledge,gst important,indirect tax,vat'),

('commerce','skills','Are certifications useful for Commerce students?',
 '✅ Yes! Recommended certifications:\n• Tally ERP / Tally Prime certification\n• GST Practitioner Certificate\n• MS Office Specialist (Excel, Word)\n• SAP FICO basics\n• Google Digital Marketing (free)\n• NPTEL accounting courses\nCertifications show initiative and industry readiness.',
 'certification,certificate,tally certification,useful certificate,commerce certification,course,professional course'),

('commerce','training','What training is given in the first year for Commerce students?',
 '📚 First Year Training:\n• Communication and personality development\n• English proficiency improvement\n• Introduction to finance and accounting concepts\n• Basic MS Office training',
 'first year training,year 1,1st year,commerce first year,training first year'),

('commerce','training','What training is given in the second year for Commerce students?',
 '📚 Second Year Training:\n• Aptitude (Quantitative, Logical, Verbal)\n• Business communication training\n• MS Excel and Tally workshops\n• Internship guidance',
 'second year training,year 2,2nd year,commerce second year,training second year'),

('commerce','training','What training is given in the final year for Commerce students?',
 '📚 Final Year Training:\n• Mock interviews (HR + technical)\n• Group discussion practice\n• Resume building workshops\n• Company-specific preparation\n• Industry expert guest sessions',
 'final year training,year 3,3rd year,last year,training final year,commerce final year'),

('commerce','interview','What should be included in a Commerce resume?',
 '📄 A strong Commerce resume must include:\n1. Name, Contact, LinkedIn\n2. Professional Objective\n3. Education (with % / CGPA)\n4. Skills (Excel, Tally, GST, SAP)\n5. Internships / Certifications\n6. Projects / Case Studies\n7. Extra-curriculars\n💡 Quantify achievements: "Prepared GST returns for 15 clients during internship"',
 'resume,cv,commerce resume,what resume,resume content,resume include,resume format,resume tips'),

('commerce','interview','What questions are asked in Commerce interviews?',
 '💼 Common interview questions for Commerce:\n• What is GST? Explain types.\n• What is the difference between capital and revenue expenditure?\n• How do you prepare a balance sheet?\n• What is bank reconciliation?\n• What are your Excel skills?\n• Why do you want to join banking/finance?\n• Tell me about yourself.\n• Where do you see yourself in 5 years?',
 'interview question,commerce interview,question asked,technical interview,bcom interview,bba interview,finance interview'),

-- ============================================================
-- ARTS & LANGUAGES DEPT
-- ============================================================

('arts','general','What type of companies hire Arts & Languages students?',
 '🎨 Companies in media, communication, education, HR, and content industries hire Arts students.\n✅ Examples: Concentrix, Sutherland, EY (Communications), Times of India, HCL BPO, MakeMyTrip, iEnergizer.\nRoles include: Content Writer, Editor, HR Executive, Teacher, Translator, Customer Support.',
 'companies,company,arts company,which company,hire arts,who recruit,english company,tamil company,media company,communication company'),

('arts','general','What is the average salary for Arts students?',
 '💰 Average salary for Arts graduates: ₹2–₹4 LPA.\n⭐ Content writing and media roles may offer ₹3–₹5 LPA with experience.\nHR and administrative roles start around ₹2.5–₹3.5 LPA.',
 'salary,package,lpa,pay,arts salary,english salary,tamil salary,average salary,income'),

('arts','general','What job roles are offered for Arts students?',
 '💼 Common roles for Arts graduates:\n• Content Writer / Copywriter\n• Editor / Sub-Editor\n• HR Executive / HR Recruiter\n• Teacher / Academic Counselor\n• Translator / Interpreter\n• Customer Support Executive\n• Communications Officer\n• Tourism / Travel Consultant\n• Visual Media Associate (for VISCOM)',
 'job role,role,position,arts job,english job,tamil job,what job,work profile,designation'),

('arts','skills','What technical skills are required for Arts students?',
 '🎨 Key skills for Arts & Language placements:\n• MS Office (Word, Excel, PowerPoint)\n• Content writing and SEO basics\n• Email and business communication\n• Social media management\n• Basic graphic design (Canva)\n• Research and report writing\n• Language proficiency (Tamil/English/Hindi)',
 'skill,technical skill,arts skill,english skill,tamil skill,what skill,important skill,soft skill,language skill'),

('arts','skills','Is communication skill important for Arts students?',
 '✅ Communication is the MOST important skill for Arts graduates!\n📌 Improve your communication by:\n• Reading newspapers daily (The Hindu, Times of India)\n• Joining debate or English clubs\n• Practicing mock interviews\n• Watching English/Tamil news channels\n• Writing blogs or articles\nMost recruiters eliminate candidates in the first round based on communication alone.',
 'communication,communication skill,english communication,speaking,spoken english,verbal,language skill,communication important'),

('arts','training','What training is given in the final year for Arts students?',
 '📚 Final Year Training for Arts:\n• Mock interviews (HR + communication rounds)\n• Group discussion practice\n• Resume building for non-technical roles\n• Content writing workshops\n• Soft skills development\n• Campus-to-corporate transition sessions',
 'final year training,last year training,arts training,training final year,3rd year training'),

('arts','interview','What questions are asked in Arts interviews?',
 '🎨 Common interview questions for Arts graduates:\n• Tell me about yourself (in 2 minutes)\n• Why did you choose this course?\n• What are your strengths as a language/arts student?\n• How would you handle a difficult customer?\n• Can you write a short paragraph on [topic]?\n• What are your career goals?\n• How do you manage deadlines?\n• Why do you want this role?',
 'interview question,arts interview,english interview,tamil interview,question asked,content writer interview,hr interview arts'),

('arts','interview','What should be included in an Arts student resume?',
 '📄 Arts student resume must include:\n1. Name, Contact, LinkedIn\n2. Objective (career goal in 2 lines)\n3. Education details\n4. Language Skills (English, Tamil, Hindi, etc.)\n5. Technical Skills (MS Office, Canva, SEO)\n6. Internships / Writing samples\n7. Certifications (Google, NPTEL)\n8. Extra-curriculars (debates, publications)\n💡 Link your writing portfolio or blog if you have one.',
 'resume,cv,arts resume,content writer resume,english resume,tamil resume,what resume,resume include'),

-- ============================================================
-- BASIC SCIENCES DEPT
-- ============================================================

('basic_science','general','What type of companies hire Basic Sciences students?',
 '🔬 Companies in science, research, analytics, healthcare, and lab industries hire Basic Sciences students.\n✅ Examples: Biotech labs, pharma companies, research institutions, ISRO, DRDO, data analytics firms.\nRoles include: Lab Technician, Research Assistant, Analyst, Quality Control Officer, Data Analyst.',
 'companies,company,science company,which company,hire,maths company,physics company,chemistry company,lab company,research'),

('basic_science','general','What is the average salary for Basic Sciences students?',
 '💰 Average salary: ₹2–₹4 LPA for industry roles.\nResearch positions may start at ₹3–₹5 LPA.\n📌 Higher studies (M.Sc, PhD) significantly increase earning potential in research and academia.',
 'salary,package,lpa,pay,science salary,maths salary,physics salary,average salary,income,stipend'),

('basic_science','skills','What technical skills are required for Basic Sciences students?',
 '🔬 Key skills for Basic Sciences placements:\n• Analytical thinking and data interpretation\n• Lab skills and instrumentation\n• Research methodology\n• MS Excel and statistical tools\n• Python or R (for data analysis roles)\n• MATLAB (for Maths/Physics students)\n• Report writing and scientific documentation',
 'skill,technical skill,science skill,maths skill,physics skill,chem skill,what skill,important skill,lab skill,research skill'),

('basic_science','training','What training is given for Basic Sciences students?',
 '📚 Training provided:\n• Aptitude and analytical reasoning\n• Research paper writing workshops\n• Mock interviews for science roles\n• Lab safety and practical skills\n• Data analysis training (Excel, Python basics)\n• Industry expert guest lectures from research orgs',
 'training,science training,what training,placement training,research training,lab training'),

('basic_science','interview','What questions are asked in Basic Sciences interviews?',
 '🔬 Common interview questions:\n• Tell me about your final year project or research.\n• What lab techniques are you proficient in?\n• How do you approach data analysis?\n• Explain a complex scientific concept simply.\n• Why are you interested in industry vs. research/academia?\n• What tools have you used (MATLAB, Python, R)?\n• Describe a problem you solved using analytical thinking.',
 'interview question,science interview,maths interview,physics interview,question asked,research interview,lab interview'),

-- ============================================================
-- MANAGEMENT DEPT (BBA, MCOM, DM)
-- ============================================================

('management','general','What type of companies hire Management students?',
 '💼 Companies in business, marketing, HR, consulting, and operations hire Management students.\n✅ Examples: Deloitte, KPMG, Amazon, Flipkart, HDFC, ICICI, Gartner, consulting firms, FMCG companies.\nRoles: HR Executive, Marketing Executive, Business Analyst, Operations Manager, Brand Associate.',
 'companies,company,management company,bba company,which company,hire management,who recruit,business company,hr company,marketing company'),

('management','general','What is the average salary for Management students?',
 '💰 Average salary: ₹3–₹5 LPA for BBA graduates.\n⭐ MCOM / MBA graduates can expect ₹5–₹8 LPA.\nMarketing and consulting roles in big firms may offer ₹6–₹10 LPA.',
 'salary,package,lpa,pay,bba salary,management salary,mcom salary,average salary,income'),

('management','skills','What skills are required for Management students?',
 '💼 Key skills for Management placements:\n• Leadership and decision making\n• Business communication (written + spoken)\n• MS Office (Excel, PowerPoint, Word)\n• CRM tools basics (Salesforce, HubSpot)\n• Marketing fundamentals\n• Data analysis and reporting\n• Project management basics\n• Presentation skills',
 'skill,management skill,bba skill,what skill,important skill,leadership,communication skill,marketing skill,hr skill,business skill'),

('management','training','What training is given in the final year for Management students?',
 '📚 Final Year Training:\n• Case study discussions\n• Mock interviews (HR + managerial rounds)\n• Group Discussion on business topics\n• Resume building for corporate roles\n• Industry expert sessions\n• Leadership workshops',
 'training,management training,bba training,what training,final year training,corporate training'),

('management','interview','What questions are asked in Management interviews?',
 '💼 Common Management interview questions:\n• Tell me about yourself and leadership experience.\n• Why do you want to join our company?\n• What is your understanding of marketing?\n• How would you handle a conflict in a team?\n• Describe a time you led a group project.\n• What are your short-term and long-term goals?\n• What is CRM / SWOT analysis / Porter\'s five forces?\n• Why should we hire you over other candidates?',
 'interview question,bba interview,management interview,question asked,hr management,business interview,corporate interview'),

-- ============================================================
-- VOCATIONAL DEPT (FASHION, AIRPORT, NFSM, CND)
-- ============================================================

('vocational','general','What type of companies hire Vocational students?',
 '🛠️ Companies in industry, technical services, aviation, hospitality, and fashion hire Vocational students.\n✅ Examples: Airlines (IndiGo, Air India), fashion houses, hotels, food processing companies, nutrition clinics.\nRoles: Technician, Cabin Crew, Fashion Designer, Nutritionist, Airport Ground Staff.',
 'companies,company,vocational company,fashion company,airport company,which company,hire,who recruit,aviation company'),

('vocational','general','What is the average salary for Vocational students?',
 '💰 Average salary: ₹2.5–₹4 LPA for most vocational roles.\n✈️ Aviation roles (cabin crew, ground staff) offer ₹3–₹5 LPA + allowances.\n🌟 Experienced fashion designers in top brands earn ₹5–₹10 LPA.',
 'salary,package,lpa,pay,vocational salary,fashion salary,airport salary,average salary,income'),

('vocational','skills','What skills are required for Vocational students?',
 '🛠️ Key skills:\n• Practical / hands-on technical skills\n• Customer service and hospitality\n• MS Office basics\n• Communication (English + regional language)\n• Problem-solving in real-world situations\n• Team coordination\n• Domain-specific skills: aviation regulations, diet planning, design software (Adobe, CorelDRAW)',
 'skill,vocational skill,fashion skill,airport skill,what skill,important skill,practical skill,technical skill,domain skill'),

('vocational','interview','What questions are asked in Vocational interviews?',
 '🛠️ Common Vocational interview questions:\n• Tell me about yourself and your vocational background.\n• Describe a hands-on project or practical experience.\n• How do you handle difficult customers or situations?\n• What safety protocols do you follow in your domain?\n• Why did you choose this vocational field?\n• What are your strengths in practical work?\n• How do you stay updated in your industry?',
 'interview question,vocational interview,fashion interview,airport interview,question asked,cabin crew interview,nutrition interview'),

-- ============================================================
-- GENERAL (applies to ALL streams)
-- ============================================================

('general','eligibility','Is attendance important for placement eligibility?',
 '✅ Yes, attendance is very important for placement eligibility.\n📌 Most placement cells require a minimum of 75% attendance.\nPoor attendance can disqualify you from attending drives — so maintain regularity.',
 'attendance,attendance important,attendance required,75%,minimum attendance,eligibility attendance'),

('general','eligibility','Is English communication necessary for placements?',
 '✅ Yes, English communication is essential for almost all placement roles.\n📌 Even for regional language jobs, basic English is expected in HR rounds.\n💡 Practice speaking English daily — with friends, in front of a mirror, or through YouTube videos.',
 'english,english communication,spoken english,communication necessary,english necessary,language requirement,verbal english'),

('general','eligibility','Is resume mandatory for placements?',
 '✅ Yes! A resume is absolutely mandatory for any placement drive.\n📌 Your resume is the first impression. A bad resume can get you rejected before the interview.\n💡 Prepare your resume by 2nd year and keep updating it.',
 'resume mandatory,resume required,need resume,resume necessary,cv mandatory,cv required'),

('general','eligibility','Can students attend multiple company interviews?',
 '✅ Yes! Most colleges allow students to attend multiple company drives unless they have already accepted an offer.\nCheck your placement cell\'s specific "offer hold" policy — some colleges restrict further drives once you accept an offer.',
 'multiple company,multiple interview,more than one,two companies,how many companies,apply multiple'),

('general','training','Are group discussions conducted?',
 '✅ Yes! Group Discussions (GD) are practiced regularly as part of placement preparation.\n📌 GD Tips:\n• Start with a strong, relevant point\n• Listen to others before responding\n• Use examples and data\n• Stay calm and assertive\n• Try to summarize at the end',
 'group discussion,gd,discussion conducted,practice gd,gd training,gd practice,group discussion training'),

('general','training','Are industry experts invited to the college?',
 '✅ Yes! The placement cell regularly invites industry experts for:\n• Guest lectures on industry trends\n• Resume review sessions\n• Interview tips from working professionals\n• Webinars and workshops\nAttend all such sessions — networking with professionals is very valuable.',
 'industry expert,guest lecture,expert invited,industry visit,speaker,professionals,webinar,workshop expert'),

('general','interview','What is an HR interview?',
 '🤝 An HR interview is a personality and behavioral round where the recruiter evaluates:\n• Your communication and confidence\n• Your self-awareness (strengths, weaknesses)\n• Your career goals and motivation\n• Cultural fit with the company\n📌 Common questions: Tell me about yourself, Why should we hire you?, Where do you see yourself in 5 years?',
 'hr interview,what is hr,hr round,hr interview meaning,hr question,human resource interview'),

('general','interview','What is a technical interview?',
 '💻 A technical interview tests your subject/domain knowledge:\n• CS students: coding, DSA, DBMS, OS, networking\n• Commerce: accounting, finance, Excel, GST\n• Arts: writing, language, communication tasks\n• Sciences: lab/research knowledge, analytical ability\n📌 Prepare your core subjects thoroughly and practice explaining concepts simply.',
 'technical interview,what is technical,technical round,tech interview,subject interview,domain interview'),

('general','interview','Is body language important in interviews?',
 '✅ Body language is very important! Recruiters judge you before you even speak.\n📌 Good body language:\n• Firm handshake\n• Maintain eye contact (not staring)\n• Sit straight and upright\n• Smile genuinely\n• Avoid fidgeting\n• Nod to show you are listening\n• Avoid crossing arms',
 'body language,posture,eye contact,handshake,gesture,expression,sitting posture,interview manner,appearance,dress'),

('general','interview','Is discipline important for getting placement?',
 '✅ Yes! Companies observe your behavior from the moment you enter the building.\n📌 Be disciplined:\n• Arrive 15 minutes early\n• Dress professionally\n• Switch off or silence your phone\n• Be polite to all staff, not just interviewers\n• Follow all instructions during tests',
 'discipline,disciplined,behavior,conduct,professional behavior,discipline important,punctual,punctuality'),

('general','general','Is placement guaranteed for all students?',
 '📌 Placement is an opportunity provided to all eligible students — it is NOT a guarantee.\nYour selection depends entirely on:\n• Your preparation and skills\n• Performance in aptitude, technical, GD, and HR rounds\n• Communication and confidence\n💡 The college opens the door — you have to walk through it.',
 'guarantee,guaranteed,placement guarantee,everyone placed,all students,assurance,promise placement');
