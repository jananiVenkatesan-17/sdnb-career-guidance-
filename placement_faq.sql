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
-- ============================================================
-- placement_faq entries for all 15 NEW courses
-- Run AFTER new_courses_dataset.sql
-- ============================================================

INSERT INTO `placement_faq` (`dept`,`category`,`question`,`answer`,`keywords`) VALUES

-- ============================================================
-- STATS (B.Sc. Statistics) — mapped to dept: basic_science
-- ============================================================
('basic_science','general','What companies hire B.Sc. Statistics students?',
 '🏢 Companies that hire Statistics graduates:\n• Analytics firms: Mu Sigma, Fractal Analytics, Latent View Analytics\n• IT companies (analytics roles): TCS, Infosys, Wipro\n• BFSI: HDFC, ICICI, Axis Bank (risk analytics)\n• Research orgs: NSSO, RBI, CSIR\n• Pharma: Biostatistics roles in clinical research\n💡 Adding Python + R + SQL makes you eligible for data analyst roles in almost any industry.',
 'statistics company,stats company,statistics job,stats job,analytics company,statistics hire,stats placement'),

('basic_science','general','What is the average salary for B.Sc. Statistics students?',
 '💰 Average salary: ₹3–₹5 LPA for Statistics graduates.\n• Data Analyst roles: ₹4–₹6 LPA\n• Research roles (govt/pharma): ₹3–₹5 LPA\n• After M.Sc. Statistics: ₹6–₹10 LPA\n• After M.Sc. + Python/ML skills: ₹8–₹15 LPA\n💡 Python and R certifications are the fastest way to raise your starting salary as a Statistics graduate.',
 'statistics salary,stats salary,average salary statistics,stats lpa,statistics package'),

('basic_science','skills','What technical skills are required for Statistics placements?',
 '🔬 Key skills for Statistics placements:\n• R Programming (mandatory for most stats roles)\n• Python (NumPy, Pandas, SciPy, matplotlib)\n• SPSS / SAS (for pharma and research roles)\n• MS Excel (advanced formulas, pivot tables)\n• SQL (data extraction and reporting)\n• Probability and Hypothesis Testing\n• Data Visualization (Tableau, Power BI basics)\n• Statistical Modeling (regression, ANOVA, time series)\n💡 R + Python + SQL is the most powerful combination for a Statistics fresher.',
 'statistics skill,stats skill,r programming,spss,sas,data analyst skill,statistical modeling'),

('basic_science','eligibility','What is the minimum CGPA for Statistics placements?',
 '📊 CGPA requirements for Statistics students:\n• Analytics firms: CGPA 6.5+ with strong Python/R skills\n• Research roles (CSIR, RBI): CGPA 7.0+ mandatory\n• IT company analytics roles: CGPA 6.0+\n• Pharma biostatistics: CGPA 6.5+\n💡 In Statistics, your project portfolio and programming skills often matter more than CGPA for private sector jobs.',
 'statistics cgpa,stats cgpa,minimum cgpa statistics,statistics eligibility,stats marks'),

('basic_science','training','What training is given for Statistics students?',
 '📚 Training for Statistics placement:\n• R and Python programming workshops\n• Data visualization and dashboard building\n• Aptitude and quantitative reasoning\n• Mock interviews for analyst roles\n• Resume building for analytics profiles\n• Statistical software (SPSS, SAS) hands-on training\n💡 Practice competitive coding on HackerRank (Statistics track) and Kaggle for real datasets.',
 'statistics training,stats training,analytics training,r training,python statistics'),

('basic_science','interview','What questions are asked in Statistics interviews?',
 '🔬 Common interview questions for Statistics graduates:\n• Explain the Central Limit Theorem and its importance.\n• What is the difference between Type I and Type II errors?\n• How do you handle missing data in a dataset?\n• What is the difference between correlation and causation?\n• Explain p-value and statistical significance.\n• Walk me through a data analysis project you completed.\n• What is overfitting in a statistical model?\n💡 Always be ready to write SQL queries and explain a regression model during analytics interviews.',
 'statistics interview,stats interview,analyst interview question,data analyst question statistics'),

-- ============================================================
-- PBPB (B.Sc. Plant Biology) — basic_science
-- ============================================================
('basic_science','general','What companies hire B.Sc. Plant Biology & Biotechnology students?',
 '🔬 Companies and organisations that hire PBPB graduates:\n• Biotech companies: Biocon, Syngenta, Mahyco\n• Pharma (plant extraction): Sun Pharma, Himalaya Drug Company\n• Research: CSIR-NBRI, NCBS, ICGEB\n• Government: Forest Dept, State Agriculture Depts\n• Seed companies: National Seeds Corporation, Mahyco\n• Teaching: School and college lecturer roles\n💡 A M.Sc. + research publication dramatically improves your prospects in plant biotechnology.',
 'pbpb company,plant biology job,plant biotechnology company,botany job,pbpb placement'),

('basic_science','skills','What skills are required for Plant Biology & Biotechnology placements?',
 '🔬 Key skills for PBPB placements:\n• Plant Tissue Culture techniques\n• PCR and gel electrophoresis\n• Microscopy (light and fluorescence)\n• Bioinformatics tools (BLAST, MEGA, CLUSTAL)\n• DNA/RNA extraction protocols\n• Basic Python or R for data analysis\n• Lab safety and GLP protocols\n• Scientific report writing\n💡 A CSIR internship certificate on your resume is highly valued in the plant science job market.',
 'pbpb skill,plant biology skill,tissue culture skill,molecular biology skill,botany skill'),

('basic_science','interview','What questions are asked in Plant Biology interviews?',
 '🔬 Common interview questions for PBPB students:\n• Explain the process and applications of plant tissue culture.\n• What is CRISPR-Cas9 and how is it used in plant improvement?\n• Describe your final year project.\n• What is the principle of PCR?\n• Explain Agrobacterium-mediated transformation.\n• What bioinformatics tools have you used?\n• What is the difference between somatic and zygotic embryogenesis?\n💡 Be ready to discuss your laboratory practicals in detail — interviewers test hands-on knowledge.',
 'pbpb interview,plant biology interview,plant biotechnology interview,botany interview question'),

-- ============================================================
-- MAHRM (M.A. Human Resource Management) — management
-- ============================================================
('management','general','What companies hire M.A. HRM graduates?',
 '💼 Top companies recruiting M.A. HRM graduates:\n• IT companies: TCS (HR roles), Infosys, Wipro, Accenture\n• Consulting: Deloitte, Mercer, Aon Hewitt\n• Banking: HDFC Bank, ICICI Bank (HR operations)\n• Staffing firms: Manpower, TeamLease, Randstad\n• E-commerce: Amazon, Flipkart (HR Operations)\n• Startups: High demand for HR generalists\n💡 HRIS tool knowledge (Workday, SAP SuccessFactors, Zoho People) significantly increases your marketability.',
 'mahrm company,hrm company,hr management job,human resource management placement,hr masters job'),

('management','skills','What skills are required for M.A. HRM placements?',
 '💼 Key skills for M.A. HRM placements:\n• Recruitment and talent acquisition\n• Performance management systems\n• Labour law (Industrial Disputes Act, Factories Act, PF, ESI)\n• HRIS tools (SAP HR, Zoho People, Darwinbox)\n• Payroll processing and compliance\n• Training and development design\n• HR analytics (MS Excel, Power BI basics)\n• Compensation and benefits management\n💡 A SHRM or PHR certification adds significant credibility to your HRM profile.',
 'hrm skill,hr management skill,mahrm skill,human resource skill,hr analytics skill'),

('management','interview','What questions are asked in M.A. HRM interviews?',
 '💼 Common interview questions for M.A. HRM graduates:\n• How would you handle a conflict between two senior employees?\n• Design a performance appraisal system for a 200-person company.\n• What HR metrics would you track and why?\n• Explain the difference between recruitment and selection.\n• What is employer branding and how do you build it?\n• Describe a time you influenced someone without authority.\n• What is the HRBP model?\n💡 Use the STAR method for all behavioral questions — specific situations score highest.',
 'hrm interview,hr management interview,mahrm interview,human resource interview question'),

-- ============================================================
-- MSW (Master of Social Work) — arts
-- ============================================================
('arts','general','What organisations hire Master of Social Work graduates?',
 '🎨 Organisations recruiting MSW graduates:\n• NGOs: CRY, Aga Khan Foundation, HelpAge India, Pratham\n• International: UNICEF, WHO, UNDP (India offices)\n• Government: Social Welfare Departments, Child Welfare Committees\n• Hospitals: Medical social workers in Apollo, Fortis\n• Corporates: CSR roles in TCS, Infosys Foundation, Tata Trusts\n• Research: Social research institutes and think tanks\n💡 Government social work roles offer excellent job security and salary. UPSC and state PSC exams include social work roles.',
 'msw company,social work job,msw placement,social worker job,ngo job social work'),

('arts','skills','What skills are required for MSW placements?',
 '🎨 Key skills for MSW graduates:\n• Community development methodology\n• Casework and counselling skills\n• Social research (survey design, data analysis)\n• Programme management and monitoring (M&E)\n• Report writing and documentation\n• Proposal writing for grants and funding\n• MS Office (Excel for data, Word for reports)\n• Communication in local languages\n💡 A proficiency in local language + English communication is a strong differentiator for MSW graduates in field roles.',
 'msw skill,social work skill,ngo skill,community development skill,casework skill'),

('arts','interview','What questions are asked in MSW interviews?',
 '🎨 Common interview questions for MSW graduates:\n• Walk me through a fieldwork project you led.\n• How would you conduct a community needs assessment?\n• What is the difference between charity and development?\n• How do you handle burnout in social work?\n• Describe a situation where a client resisted your help.\n• What is PRA (Participatory Rural Appraisal)?\n• How would you design an intervention for urban homeless youth?\n💡 MSW interviews heavily test your fieldwork experience — have 2–3 detailed field stories ready.',
 'msw interview,social work interview,ngo interview,community development interview'),

-- ============================================================
-- MSPB (M.Sc. Plant Biology PG) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Plant Biology & Biotechnology graduates?',
 '🔬 Companies and organisations for M.Sc. PBPB graduates:\n• Biotech R&D: Biocon, Serum Institute, Syngenta\n• Pharma (plant-based APIs): Himalaya, Dabur Research\n• Government Research: CSIR-NBRI, ICAR, ICGEB, NCBS\n• Seed companies: Pioneer (Corteva), Mahyco, BAYER CropScience\n• Academia: Lecturer / Research Associate positions\n• Bioinformatics companies: Strand Life Sciences\n💡 A peer-reviewed publication from your M.Sc. dissertation sets you apart dramatically in research-track hiring.',
 'mspb company,plant biotech pg job,msc plant biology job,plant science research job'),

('basic_science','skills','What skills are required for M.Sc. Plant Biology placements?',
 '🔬 Advanced skills for M.Sc. PBPB placements:\n• CRISPR-Cas9 gene editing\n• Flow cytometry and confocal microscopy\n• Proteomics and metabolomics basics\n• Bioinformatics (BLAST, MEGA, Biopython, R)\n• Western blotting and ELISA\n• RNA-seq analysis\n• Scientific writing (research papers, grants)\n• Agrobacterium transformation protocol\n💡 Apply for JRF (CSIR-UGC NET) if targeting research — it provides a fellowship stipend and opens prestigious research positions.',
 'mspb skill,plant biotech skill,msc plant biology skill,molecular plant science skill,crispr plant'),

-- ============================================================
-- MSBIO (M.Sc. Biostatistics) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Biostatistics graduates?',
 '🔬 Top companies for Biostatistics graduates:\n• CROs (Contract Research): IQVIA, Parexel, Quintiles, Covance\n• Pharma: Pfizer India, AstraZeneca, Cipla R&D\n• Government: ICMR, NIH-funded projects\n• Analytics: Mu Sigma, Fractal (health analytics division)\n• Hospitals: Clinical research units\n• Academic research: Biostatistics faculty positions\n💡 SAS certification is the gold standard for clinical trial biostatistician roles — prioritise this.',
 'msbio company,biostatistics job,msc biostatistics job,clinical trial statistician,cro job'),

('basic_science','skills','What skills are required for Biostatistics placements?',
 '🔬 Key skills for M.Sc. Biostatistics:\n• SAS (most demanded in clinical research)\n• R programming (survival analysis, meta-analysis)\n• Clinical trial design (phases, randomisation, blinding)\n• Epidemiological methods (incidence, prevalence, odds ratio)\n• Statistical Programming (Python, Stata)\n• Data management (clinical databases, EDC systems)\n• Good Clinical Practice (GCP) knowledge\n• Scientific and regulatory writing\n💡 A CDISC SDTM knowledge + SAS certification makes you immediately job-ready in pharma CRO roles.',
 'msbio skill,biostatistics skill,clinical trial skill,sas programming,survival analysis skill'),

('basic_science','interview','What questions are asked in Biostatistics interviews?',
 '🔬 Common interview questions for Biostatistics graduates:\n• Explain the phases of a clinical trial.\n• What is the difference between ITT and per-protocol analysis?\n• How do you calculate sample size for a two-arm RCT?\n• What is a Kaplan-Meier curve?\n• Explain Type I and Type II errors in the context of drug trials.\n• What is Bonferroni correction and when do you use it?\n• Describe the logistic regression model and its output interpretation.\n💡 Be ready to write or explain SAS PROC code — many CRO interviews include a practical coding test.',
 'msbio interview,biostatistics interview,clinical research interview,sas interview,cro interview'),

-- ============================================================
-- MSPHY (M.Sc. Physics) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Physics graduates?',
 '🔬 Companies and organisations hiring M.Sc. Physics graduates:\n• Government research: ISRO, DRDO, BARC, CSIR labs\n• IT companies (analytics/modelling roles): TCS Research, IBM Research\n• Semiconductor/electronics: Texas Instruments, STMicroelectronics\n• Teaching: IIT/NIT/Engineering colleges (with GATE/NET)\n• Analytics firms: Quantitative modelling roles\n• Finance: Quantitative analyst (quant) roles in investment banks\n💡 GATE Physics + JEST are the most important exams for M.Sc. Physics graduates targeting high-paying research roles.',
 'msphy company,physics pg job,msc physics job,physics placement,research job physics'),

('basic_science','skills','What skills are required for M.Sc. Physics placements?',
 '🔬 Key skills for M.Sc. Physics placements:\n• Computational Physics (Python, MATLAB, C++)\n• Experimental techniques (XRD, Spectroscopy, TEM/SEM)\n• Mathematical modelling and simulation\n• MATLAB/Simulink\n• LaTeX for scientific documentation\n• Data analysis (NumPy, SciPy, matplotlib)\n• Semiconductor device physics (for industry roles)\n• Quantum mechanics and electrodynamics (for research roles)\n💡 Python + Computational Physics skills open IT and quant finance roles to Physics graduates.',
 'msphy skill,physics skill,computational physics,matlab skill,msc physics skill,experimental physics'),

-- ============================================================
-- MSCHEM (M.Sc. Chemistry) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Chemistry graduates?',
 '🔬 Companies hiring M.Sc. Chemistry graduates:\n• Pharma: Cipla, Dr. Reddy\'s, Sun Pharma, Aurobindo\n• Chemical companies: Pidilite, UPL, Aarti Industries\n• Research: CSIR-IICT, CSIR-NCL, CFTRI\n• Cosmetics/FMCG: HUL (R&D), P&G, Marico\n• Testing labs: SGS India, Bureau Veritas, Eurofins\n• Government: FSSAI, BIS, Drug Inspectorate\n💡 GATE Chemistry is a strong pathway to prestigious research positions at IITs and CSIR labs.',
 'mschem company,chemistry pg job,msc chemistry job,pharma chemistry job,analytical chemistry job'),

('basic_science','skills','What skills are required for M.Sc. Chemistry placements?',
 '🔬 Key skills for M.Sc. Chemistry placements:\n• Analytical instruments: HPLC, GC, GC-MS, LC-MS\n• Spectroscopy: NMR, IR, UV-Vis, Mass Spec\n• Good Laboratory Practice (GLP)\n• Analytical method validation (ICH Q2R1)\n• Organic synthesis techniques\n• ChemDraw for structure drawing\n• Python/MATLAB for data analysis\n• Regulatory knowledge: FSSAI, USP, BP\n💡 HPLC + method validation skills are the most in-demand for pharma QC and R&D roles.',
 'mschem skill,chemistry skill,hplc skill,analytical chemistry skill,organic chemistry skill'),

('basic_science','interview','What questions are asked in M.Sc. Chemistry interviews?',
 '🔬 Common interview questions for M.Sc. Chemistry graduates:\n• Explain your dissertation research and its industry relevance.\n• What is HPLC? When would you use it over GC?\n• Explain the principle of NMR spectroscopy.\n• What is ICH Q2(R1) method validation?\n• How do you prepare a 0.1 M solution of NaOH?\n• What safety protocols do you follow when handling hazardous chemicals?\n• What is retrosynthetic analysis?\n💡 Pharma QC interviews often include a practical question — be ready to describe an HPLC run procedure step by step.',
 'mschem interview,chemistry interview,pharma chemistry interview,analytical interview,hplc interview'),

-- ============================================================
-- MSHSN (M.Sc. Food Science Nutrition) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Food Science Nutrition graduates?',
 '🔬 Companies hiring M.Sc. MSHSN graduates:\n• Hospitals: Apollo, Fortis, AIIMS (Clinical Dietician)\n• Food industry: Nestlé, ITC Foods, Britannia, Amul\n• Pharma nutrition: Abbott Nutrition, Danone, PepsiCo R&D\n• Government: FSSAI, NIN, ICMR-NIN\n• Wellness companies: Healthify, Cult.fit, Portea\n• Research: Food quality and safety labs\n💡 An RD (Registered Dietitian) registration after your M.Sc. is a powerful credential for clinical roles.',
 'mshsn company,food science job,nutrition job,dietitian job,clinical nutrition job'),

('basic_science','skills','What skills are required for M.Sc. Food Science placements?',
 '🔬 Key skills for MSHSN placements:\n• Clinical diet planning (therapeutic diets: diabetic, renal, cardiac)\n• HACCP and FSSAI food safety compliance\n• Proximate analysis and nutritional assessment\n• Diet analysis software (Dietplan, NutriAssist)\n• Food product development and quality control\n• MS Excel for dietary tracking\n• Patient counselling and communication\n• Research methodology and clinical study design\n💡 FSSAI Food Safety Supervisor certification + clinical internship hours = most hireable MSHSN graduate profile.',
 'mshsn skill,food science skill,nutrition skill,dietitian skill,haccp skill,clinical nutrition skill'),

-- ============================================================
-- MSDAI (M.Sc. Data Science and AI) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Data Science and AI graduates?',
 '🔬 Top companies for M.Sc. MSDAI graduates:\n• Product companies: Google, Microsoft, Amazon, Adobe\n• IT MNCs: TCS Research, Infosys AI, Wipro Holmes\n• Analytics: Fractal, Mu Sigma, Tiger Analytics\n• Startups: Dozens of AI/ML startups in Bengaluru/Hyderabad\n• FinTech: Razorpay, Zepto (data teams)\n• Healthcare AI: Niramai, SigTuple\n💡 A strong GitHub portfolio with ML projects + Kaggle competition rankings significantly increases your shortlisting rate.',
 'msdai company,data science job,ai job,machine learning job,msc data science job,artificial intelligence placement'),

('basic_science','skills','What skills are required for M.Sc. Data Science & AI placements?',
 '🔬 Key skills for MSDAI placements:\n• Machine Learning (sklearn, XGBoost, LightGBM)\n• Deep Learning (TensorFlow, PyTorch, Keras)\n• NLP (NLTK, spaCy, HuggingFace Transformers)\n• Python (Pandas, NumPy, Matplotlib, Seaborn)\n• SQL and Big Data basics (Spark, Hive)\n• Model Deployment (Flask, FastAPI, Docker basics)\n• Computer Vision (OpenCV, CNNs)\n• MLOps and experiment tracking (MLflow)\n💡 Kaggle + GitHub + a deployed ML project (on Streamlit or Hugging Face Spaces) is the gold standard portfolio.',
 'msdai skill,data science skill,machine learning skill,deep learning skill,ai skill,tensorflow,pytorch'),

('basic_science','interview','What questions are asked in Data Science & AI interviews?',
 '🔬 Common interview questions for MSDAI graduates:\n• Explain the bias-variance tradeoff.\n• What is the difference between bagging and boosting?\n• How do you handle an imbalanced dataset?\n• Explain attention mechanism in transformers.\n• What metrics do you use beyond accuracy for classification?\n• Walk me through a complete ML pipeline you built.\n• What is regularisation and why is it needed?\n💡 Most data science interviews include a take-home assignment or a live coding test — practise on Kaggle datasets daily.',
 'msdai interview,data science interview,machine learning interview,ai interview,ml interview question'),

-- ============================================================
-- MAECO (M.A. Economics) — arts
-- ============================================================
('arts','general','What companies hire M.A. Economics graduates?',
 '🎨 Organisations and companies hiring M.A. Economics graduates:\n• Government: RBI, SEBI, NITI Aayog, Ministry of Finance\n• Consulting: McKinsey, BCG, Deloitte Economic Advisory\n• Banking: SBI, HDFC, ICICI (economic research teams)\n• International: World Bank, IMF, Asian Development Bank\n• Think tanks: NIPFP, NCAER, IEG Delhi\n• Research: IGIDR, ISI, IIM economics departments\n💡 The RBI Grade B exam is the most coveted direct government job for Economics graduates — start preparing from M.A. year 1.',
 'maeco company,economics job,ma economics job,economic research job,rbi job economics'),

('arts','skills','What skills are required for M.A. Economics placements?',
 '🎨 Key skills for M.A. Economics placements:\n• Econometrics (OLS, IV, Panel Data, Time Series)\n• Stata / EViews (most commonly used in economics research)\n• R programming (for academic and policy research)\n• Python (pandas, statsmodels — for analytics roles)\n• Macro and microeconomic policy analysis\n• Academic and policy writing\n• Data visualisation (Tableau, matplotlib)\n• MS Excel (Advanced — for financial sector roles)\n💡 Stata + econometric modelling + a working paper or publication is the strongest profile for economics research hiring.',
 'maeco skill,economics skill,econometrics skill,stata skill,economic research skill'),

('arts','interview','What questions are asked in M.A. Economics interviews?',
 '🎨 Common interview questions for M.A. Economics graduates:\n• Explain the difference between monetary and fiscal policy.\n• What is your dissertation topic and methodology?\n• Explain the Phillips curve and its current relevance.\n• What is the Gini coefficient and what does India\'s value indicate?\n• How would you design a study to evaluate a government welfare scheme?\n• What is cointegration in time series?\n• Explain comparative advantage and its criticism.\n💡 RBI and SEBI interviews are highly technical — prepare macroeconomic theory, Indian economy data, and econometric methods in depth.',
 'maeco interview,economics interview,rbi interview economics,policy interview economics'),

-- ============================================================
-- MSCP (M.Sc. Counselling Psychology) — basic_science
-- ============================================================
('basic_science','general','What organisations hire M.Sc. Counselling Psychology graduates?',
 '🔬 Organisations hiring M.Sc. Counselling Psychology graduates:\n• Hospitals: Apollo, Fortis (psychiatry departments)\n• Mental health NGOs: iCall (TISS), Vandrevala Foundation, Mann Talks\n• Schools/Colleges: School counsellors (mandatory in many CBSE schools)\n• Corporate: Employee Assistance Programme (EAP) counsellors\n• Government: NHM counsellors, NIMHANS-affiliated clinics\n• Private practice: Individual therapy (after RCI registration)\n💡 RCI (Rehabilitation Council of India) registration is mandatory to practice as a clinical/counselling psychologist in India.',
 'mscp company,counselling psychology job,psychologist job,therapist job,mental health job,school counsellor'),

('basic_science','skills','What skills are required for Counselling Psychology placements?',
 '🔬 Key skills for M.Sc. Counselling Psychology:\n• Therapeutic modalities: CBT, DBT, Person-Centred, EMDR\n• Psychological assessment tools: PHQ-9, GAD-7, MMSE, BDI\n• Case conceptualisation and treatment planning\n• Crisis intervention and suicide risk assessment\n• Ethics and confidentiality in counselling\n• Research methods and SPSS/R\n• Documentation and clinical report writing\n• Mindfulness and psychoeducation facilitation\n💡 At least 600 hours of supervised clinical placement is required for RCI registration — track your hours carefully.',
 'mscp skill,counselling skill,psychology skill,cbt skill,assessment skill,therapy skill'),

('basic_science','interview','What questions are asked in Counselling Psychology interviews?',
 '🔬 Common interview questions for M.Sc. Counselling Psychology graduates:\n• Describe a challenging counselling case and your approach.\n• How do you conduct a risk assessment for suicidal ideation?\n• What is your primary theoretical orientation and why?\n• How do you maintain professional boundaries with clients?\n• What is the difference between CBT and DBT?\n• How would you handle a client who discloses abuse during a session?\n• Explain confidentiality limits in the Indian context.\n💡 Always have 2–3 anonymised case examples ready — clinical interviews are largely experiential, not theoretical.',
 'mscp interview,counselling psychology interview,psychologist interview,therapy interview,mental health interview'),

-- ============================================================
-- MSAM (M.Sc. Applicable Mathematics) — basic_science
-- ============================================================
('basic_science','general','What companies hire M.Sc. Applicable Mathematics graduates?',
 '🔬 Companies hiring M.Sc. Applicable Mathematics graduates:\n• Data Science/Analytics: all major analytics firms\n• FinTech: Quantitative analyst roles (Jane Street, Goldman Sachs algo teams)\n• IT: TCS Research, IBM Research, Infosys AI\n• Operations Research: logistics, supply chain optimisation firms\n• Insurance: Actuarial analyst roles (with additional CT exams)\n• Government: DRDO, ISRO (modelling roles)\n💡 GATE Mathematics is a strong gateway to IIT research positions and PSU roles for Applicable Maths graduates.',
 'msam company,applicable mathematics job,msc maths job,operations research job,quantitative analyst job'),

('basic_science','skills','What skills are required for M.Sc. Applicable Mathematics placements?',
 '🔬 Key skills for M.Sc. Applicable Mathematics:\n• Mathematical modelling and optimisation\n• Operations Research (LP, Integer Programming, Network Flow)\n• Python (NumPy, SciPy, SymPy, OR-Tools)\n• MATLAB / Mathematica\n• Numerical methods (Runge-Kutta, FEM basics)\n• Statistics and probability theory\n• Graph theory and combinatorics\n• LaTeX for mathematical documentation\n💡 Python + Operations Research (scipy.optimize, OR-Tools) is the most industry-ready skill combo for this degree.',
 'msam skill,applicable maths skill,operations research skill,mathematical modeling skill,python math'),

-- ============================================================
-- MCOMAF (M.Com Accounting and Finance) — commerce
-- ============================================================
('commerce','general','What companies hire M.Com Accounting and Finance graduates?',
 '💼 Top companies for M.Com (Accounting and Finance) graduates:\n• Big 4 Audit Firms: Deloitte, EY, KPMG, PwC (senior roles)\n• Banking: HDFC, ICICI, SBI (financial analysis)\n• Corporate Finance: Tata Group, Mahindra (Treasury/Finance)\n• FinTech: Razorpay, Zepto, Paytm (Finance operations)\n• Government: CAG, ICAI-affiliated training roles\n• Consulting: Financial advisory in mid-tier firms\n💡 A simultaneous CMA or CPA pursuit while doing M.Com dramatically elevates your earning potential to ₹8–15 LPA.',
 'mcomaf company,mcom job,accounting finance job,mcom placement,financial analyst job mcom'),

('commerce','skills','What skills are required for M.Com Accounting and Finance placements?',
 '💼 Key skills for M.Com (AF) placements:\n• Advanced Accounting (Ind AS, IFRS, Consolidation)\n• Financial Analysis (ratio analysis, DCF valuation)\n• Tally Prime / SAP FICO\n• MS Excel (Advanced — Power Query, Pivot, VLOOKUP, Macros)\n• Direct and Indirect Taxation (GST, Income Tax)\n• Auditing (statutory, internal, forensic)\n• Python for Finance (basic financial modelling)\n• XBRL and MCA compliance\n💡 Ind AS + financial modelling + Excel proficiency is the core hiring criteria for most M.Com (AF) roles in Big 4 and corporate finance.',
 'mcomaf skill,mcom accounting skill,ifrs skill,financial modeling skill,sap fico skill,advanced accounting'),

('commerce','interview','What questions are asked in M.Com Accounting and Finance interviews?',
 '💼 Common interview questions for M.Com (AF) graduates:\n• Walk me through how you prepare a cash flow statement.\n• Explain the difference between Ind AS and IFRS revenue recognition.\n• What is deferred tax and how is it treated in financial statements?\n• How do you perform ratio analysis for a company\'s health?\n• What is XBRL filing and who needs to comply?\n• Explain the Black-Scholes option pricing model simply.\n• What is forensic accounting?\n💡 Big 4 interviews often include a short Excel or accounting case test — practise financial statement analysis and ratio computation.',
 'mcomaf interview,mcom interview,accounting finance interview,big4 interview,financial analyst interview'),

-- ============================================================
-- MBA — commerce
-- ============================================================
('commerce','general','What companies hire MBA graduates?',
 '💼 Top companies recruiting MBA graduates:\n• Consulting: McKinsey, BCG, Bain, Deloitte, KPMG\n• FMCG: HUL, ITC, P&G (Management Trainee programs)\n• E-commerce: Amazon, Flipkart (Operations/Product)\n• BFSI: HDFC Bank, Axis, Kotak, Bajaj Finserv\n• IT/Tech: TCS, Infosys, IBM (business roles)\n• Startups: Numerous growth/product/strategy roles\n💡 MBA salary range is widest of any degree: ₹6–30+ LPA depending on specialisation, college, and company.',
 'mba company,mba placement,mba job,management job,mba hiring,consulting job mba'),

('commerce','general','What is the average salary for MBA graduates?',
 '💰 MBA salary ranges:\n• Tier-1 MBA (IIMs, ISB): ₹15–30+ LPA\n• Tier-2 MBA (private colleges): ₹6–12 LPA\n• Marketing specialisation: ₹6–12 LPA fresher\n• Finance specialisation: ₹7–15 LPA\n• HR specialisation: ₹5–9 LPA\n• Operations specialisation: ₹6–10 LPA\n💡 MBA salary depends heavily on your college tier, specialisation, and internship quality. Aim for a strong summer internship — it often converts to a PPO.',
 'mba salary,mba package,mba average salary,management salary,mba lpa,mba ctc'),

('commerce','skills','What skills are required for MBA placements?',
 '💼 Key skills for MBA placements:\n• Strategic thinking and business analysis\n• Financial modelling (DCF, LBO for finance specialisation)\n• Marketing analytics and CRM (Salesforce, HubSpot)\n• Operations and supply chain fundamentals\n• MS Excel + Power BI (dashboards and data analysis)\n• Python for business analytics\n• Leadership and team management\n• Professional communication and presentation\n• Case study methodology (McKinsey, BCG frameworks)\n💡 Consulting MBA interviews are case-based. Practise 50+ cases on CasePrepCo or Victor Cheng\'s resources before your interviews.',
 'mba skill,mba technical skill,consulting skill,mba analytics,financial modeling mba,mba communication'),

('commerce','eligibility','What is the minimum CGPA for MBA placements?',
 '📊 CGPA requirements for MBA placement:\n• Consulting firms (McKinsey, Deloitte): CGPA 7.0+ with strong case performance\n• FMCG (HUL, ITC): CGPA 6.5+ with leadership activities\n• Banking and Finance: CGPA 6.5+ with finance coursework\n• IT companies: CGPA 6.0+\n• Startups: Skills and internship experience often outweigh CGPA\n💡 In MBA, your summer internship performance and pre-placement offers (PPOs) are more important than CGPA for most companies.',
 'mba cgpa,mba eligibility,mba marks,mba academic requirement,mba gpa'),

('commerce','training','What training is given for MBA placements?',
 '📚 MBA placement training typically includes:\n• Case study workshops (consulting prep)\n• Financial modelling bootcamps\n• GD and PI preparation sessions\n• Company-specific preparation (HUL LEAP, Amazon bar-raiser style)\n• Resume and LinkedIn workshops\n• Guest lectures from senior alumni\n• Mock interviews with industry professionals\n💡 Start preparing for summer internship placements from Day 1 of your MBA — the entire first year builds towards your internship.',
 'mba training,mba placement preparation,mba mock interview,case study training,mba gd pi'),

('commerce','interview','What questions are asked in MBA interviews?',
 '💼 Common MBA interview questions:\n• Walk me through your resume and key achievements.\n• Why MBA? Why this company? Why this specialisation?\n• Give me a STAR example of leadership under pressure.\n• How would you price this product? (pricing case)\n• Perform a SWOT analysis of our company.\n• What is your 5-year plan post-MBA?\n• Why should we hire you over other MBA graduates?\n💡 MBA HR interviews are more rigorous than undergraduate — every answer should be backed by a specific, quantified example.',
 'mba interview,mba interview question,mba hr question,mba technical interview,consulting interview mba');
