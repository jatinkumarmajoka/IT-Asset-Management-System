<?php

namespace App\Services\Imports;

use App\Models\Category;
use App\Models\Department;

class ImportMatcher
{
    /**
     * Predefined physical location options.
     */
    public const PREDEFINED_LOCATIONS = [
        'Research Lab',
        'Equipment Lab',
        'Computer Lab',
        'Classroom',
        'Lecture Hall',
        'Office',
        'Store Room',
        'Workshop',
        'Library',
        'Other',
    ];

    /**
     * Department aliases - configurable mapping from Excel values to database departments.
     * Can be extended without code changes by updating this map.
     * Keys are normalized (lowercase, trimmed) Excel values.
     * Values must match the exact department names in the database.
     */
    protected array $departmentAliases = [
        // Common university department abbreviations
        'evs' => 'Department of Environmental Studies',
        'evs lab' => 'Department of Environmental Studies',
        'environmental science lab' => 'Department of Environmental Studies',
        'soet' => 'School of Engineering and Technology',
        'school of engineering' => 'School of Engineering and Technology',
        'school of engineering & technology' => 'School of Engineering and Technology',
        'ict' => 'ICT Section',
        'information technology' => 'ICT Section',
        'it' => 'ICT Section',
        'computer science' => 'Department of Computer Science and Engineering',
        'cs' => 'Department of Computer Science and Engineering',
        'computer center' => 'Computer Centre',
        'computer centre' => 'Computer Centre',
        'ece' => 'Department of Electrical Engineering', // Electronics & Communication Engineering typically under Electrical
        'eee' => 'Department of Electrical Engineering',
        'mech' => 'Department of Mechanical Engineering', // Not in seeder, will fallback to partial
        'civil' => 'Department of Civil Engineering',
        'chem' => 'Department of Chemistry',
        'physics' => 'Department of Physics and Astrophysics',
        'maths' => 'Department of Mathematics',
        'math' => 'Department of Mathematics',
        'bio' => 'Department of Biotechnology',
        'biotech' => 'Department of Biotechnology',
        'management' => 'Department of Management Studies',
        'mba' => 'Department of Management Studies',
        'commerce' => 'Department of Commerce',
        'arts' => 'Department of Applied Sciences and Humanities',
        'humanities' => 'Department of Applied Sciences and Humanities',
        'law' => 'Department of Law',
        'pharmacy' => 'Department of Pharmaceutical Sciences',
        'nursing' => 'Department of Nursing', // Not in seeder
        'medical' => 'Medical Centre',
        'dental' => 'Department of Dental Sciences', // Not in seeder
        'hospital' => 'Department of Hospital Administration', // Not in seeder
        'agriculture' => 'Department of Agricultural Sciences', // Not in seeder
        'veterinary' => 'Department of Veterinary Sciences', // Not in seeder
        'architecture' => 'Department of Architecture', // Not in seeder
        'planning' => 'Department of Urban Planning', // Not in seeder
        'design' => 'Department of Design', // Not in seeder
        'fine arts' => 'Department of Fine Arts', // Not in seeder
        'music' => 'Department of Music', // Not in seeder
        'dance' => 'Department of Dance', // Not in seeder
        'theatre' => 'Department of Theatre Arts', // Not in seeder
        'journalism' => 'Department of Journalism and Mass Communication',
        'mass comm' => 'Department of Journalism and Mass Communication',
        'media' => 'Department of Journalism and Mass Communication',
        'social work' => 'Department of Sociology', // Closest match
        'psychology' => 'Department of Psychology',
        'sociology' => 'Department of Sociology',
        'economics' => 'Department of Economics',
        'history' => 'Department of History and Archaeology',
        'political science' => 'Department of Political Science',
        'pol science' => 'Department of Political Science',
        'philosophy' => 'Department of Philosophy', // Not in seeder
        'library science' => 'Department of Library and Information Science',
        'physical education' => 'Department of Physical Education and Sports',
        'sports' => 'Department of Physical Education and Sports',
        'yoga' => 'Department of Yoga',
        'home science' => 'Department of Home Science', // Not in seeder
        'nutrition' => 'Department of Nutrition Biology',
        'food technology' => 'Department of Nutrition Biology', // Closest
        'dairy' => 'Department of Dairy Technology', // Not in seeder
        'fisheries' => 'Department of Fisheries Science', // Not in seeder
        'forestry' => 'Department of Forestry', // Not in seeder
        'horticulture' => 'Department of Horticulture', // Not in seeder
        'seed' => 'Department of Seed Technology', // Not in seeder
        'plant pathology' => 'Department of Plant Pathology', // Not in seeder
        'entomology' => 'Department of Entomology', // Not in seeder
        'nematology' => 'Department of Nematology', // Not in seeder
        'soil science' => 'Department of Soil Science', // Not in seeder
        'agronomy' => 'Department of Agronomy', // Not in seeder
        'genetics' => 'Department of Genetics and Plant Breeding', // Not in seeder
        'biochemistry' => 'Department of Biochemistry',
        'microbiology' => 'Department of Microbiology',
        'botany' => 'Department of Botany', // Not in seeder
        'zoology' => 'Department of Zoology', // Not in seeder
        'geology' => 'Department of Geology', // Not in seeder
        'geography' => 'Department of Geography',
        'remote sensing' => 'Department of Remote Sensing', // Not in seeder
        'gis' => 'Department of Geoinformatics', // Not in seeder
        'statistics' => 'Department of Statistics',
        'operations research' => 'Department of Operations Research', // Not in seeder
        'computer applications' => 'Department of Computer Applications', // Not in seeder
        'bca' => 'Department of Computer Applications', // Not in seeder
        'mca' => 'Department of Computer Applications', // Not in seeder
        'bsc it' => 'Department of Computer Sciences and Information Technology',
        'msc it' => 'Department of Computer Sciences and Information Technology',
        'btech' => 'School of Engineering and Technology',
        'mtech' => 'School of Engineering and Technology',
        'be' => 'School of Engineering and Technology',
        'me' => 'School of Engineering and Technology',
        'diploma' => 'Department of Vocational Studies and Skill Development',
        'polytechnic' => 'Department of Vocational Studies and Skill Development',
        'iti' => 'Department of Vocational Studies and Skill Development',
        'vocational' => 'Department of Vocational Studies and Skill Development',

        // Department of Applied Sciences and Humanities
        'ash' => 'Department of Applied Sciences and Humanities',
        'applied sciences' => 'Department of Applied Sciences and Humanities',
        'applied sciences and humanities' => 'Department of Applied Sciences and Humanities',
        'dash' => 'Department of Applied Sciences and Humanities',

        // Department of Biochemistry
        'biochem' => 'Department of Biochemistry',
        'biochemistry dept' => 'Department of Biochemistry',

        // Department of Biotechnology
        'biotech' => 'Department of Biotechnology',
        'biotech dept' => 'Department of Biotechnology',

        // Department of Chemistry
        'chem' => 'Department of Chemistry',
        'chemistry' => 'Department of Chemistry',
        'chemistry dept' => 'Department of Chemistry',

        // Department of Civil Engineering
        'civil' => 'Department of Civil Engineering',
        'civil engg' => 'Department of Civil Engineering',
        'civil engineering dept' => 'Department of Civil Engineering',

        // Department of Commerce
        'comm' => 'Department of Commerce',
        'commerce dept' => 'Department of Commerce',

        // Department of Computer Science and Engineering
        'cse' => 'Department of Computer Science and Engineering',
        'cs' => 'Department of Computer Science and Engineering',
        'cs dept' => 'Department of Computer Science and Engineering',
        'computer science' => 'Department of Computer Science and Engineering',
        'cse dept' => 'Department of Computer Science and Engineering',

        // Department of Computer Sciences and Information Technology
        'csit' => 'Department of Computer Sciences and Information Technology',
        'cs it' => 'Department of Computer Sciences and Information Technology',

        // Department of Economics
        'econ' => 'Department of Economics',
        'economics dept' => 'Department of Economics',

        // Department of Electrical Engineering
        'ee' => 'Department of Electrical Engineering',
        'elec engg' => 'Department of Electrical Engineering',
        'electrical engg' => 'Department of Electrical Engineering',
        'electrical dept' => 'Department of Electrical Engineering',

        // Department of English and Foreign Languages
        'efl' => 'Department of English and Foreign Languages',
        'english dept' => 'Department of English and Foreign Languages',
        'english' => 'Department of English and Foreign Languages',

        // Department of Environmental Studies
        'env' => 'Department of Environmental Studies',
        'evs' => 'Department of Environmental Studies',
        'environmental studies' => 'Department of Environmental Studies',
        'environment' => 'Department of Environmental Studies',

        // Department of Geography
        'geog' => 'Department of Geography',
        'geography dept' => 'Department of Geography',

        // Department of Hindi
        'hindi' => 'Department of Hindi',
        'hindi dept' => 'Department of Hindi',

        // Department of History and Archaeology
        'hist' => 'Department of History and Archaeology',
        'history' => 'Department of History and Archaeology',
        'archaeology' => 'Department of History and Archaeology',

        // Department of Journalism and Mass Communication
        'jmc' => 'Department of Journalism and Mass Communication',
        'journalism' => 'Department of Journalism and Mass Communication',
        'mass comm' => 'Department of Journalism and Mass Communication',
        'mass communication' => 'Department of Journalism and Mass Communication',

        // Department of Law
        'law' => 'Department of Law',
        'law dept' => 'Department of Law',

        // Department of Library and Information Science
        'lis' => 'Department of Library and Information Science',
        'library science' => 'Department of Library and Information Science',

        // Department of Management Studies
        'mgmt' => 'Department of Management Studies',
        'management' => 'Department of Management Studies',
        'mba' => 'Department of Management Studies',

        // Department of Mathematics
        'math' => 'Department of Mathematics',
        'maths' => 'Department of Mathematics',
        'mathematics' => 'Department of Mathematics',
        'math dept' => 'Department of Mathematics',

        // Department of Microbiology
        'micro' => 'Department of Microbiology',
        'microbiology' => 'Department of Microbiology',

        // Department of Nutrition Biology
        'nutbio' => 'Department of Nutrition Biology',
        'nutrition biology' => 'Department of Nutrition Biology',

        // Department of Pharmaceutical Sciences
        'pharm' => 'Department of Pharmaceutical Sciences',
        'pharma' => 'Department of Pharmaceutical Sciences',
        'pharmacy' => 'Department of Pharmaceutical Sciences',

        // Department of Physical Education and Sports
        'pes' => 'Department of Physical Education and Sports',
        'physical education' => 'Department of Physical Education and Sports',
        'sports' => 'Department of Physical Education and Sports',
        'pes dept' => 'Department of Physical Education and Sports',

        // Department of Physics and Astrophysics
        'phyastro' => 'Department of Physics and Astrophysics',
        'physics' => 'Department of Physics and Astrophysics',
        'physics dept' => 'Department of Physics and Astrophysics',
        'astrophysics' => 'Department of Physics and Astrophysics',

        // Department of Political Science
        'pol sci' => 'Department of Political Science',
        'political science' => 'Department of Political Science',
        'pol science' => 'Department of Political Science',

        // Department of Printing and Packaging Technology
        'ppt' => 'Department of Printing and Packaging Technology',
        'printing' => 'Department of Printing and Packaging Technology',
        'packaging' => 'Department of Printing and Packaging Technology',

        // Department of Psychology
        'psy' => 'Department of Psychology',
        'psychology' => 'Department of Psychology',

        // Department of Sanskrit
        'sans' => 'Department of Sanskrit',
        'sanskrit' => 'Department of Sanskrit',

        // Department of Sociology
        'soc' => 'Department of Sociology',
        'sociology' => 'Department of Sociology',

        // Department of Statistics
        'stat' => 'Department of Statistics',
        'stats' => 'Department of Statistics',
        'statistics' => 'Department of Statistics',

        // Department of Tourism and Hotel Management
        'thm' => 'Department of Tourism and Hotel Management',
        'tourism' => 'Department of Tourism and Hotel Management',
        'hotel management' => 'Department of Tourism and Hotel Management',

        // Department of Yoga
        'yoga' => 'Department of Yoga',

        // Department of Vocational Studies and Skill Development
        'vssd' => 'Department of Vocational Studies and Skill Development',
        'vocational studies' => 'Department of Vocational Studies and Skill Development',
        'skill development' => 'Department of Vocational Studies and Skill Development',

        // Department of Teacher Education
        'teacher ed' => 'Department of Teacher Education',
        'teacher education' => 'Department of Teacher Education',
        'teacher training' => 'Department of Teacher Education',
        'teachered' => 'Department of Teacher Education',

        // Department of Applied Sciences and Humanities
        'ash' => 'Department of Applied Sciences and Humanities',
        'applied sciences' => 'Department of Applied Sciences and Humanities',
        'applied sciences and humanities' => 'Department of Applied Sciences and Humanities',
        'dash' => 'Department of Applied Sciences and Humanities',

        // Department of Biochemistry
        'biochem' => 'Department of Biochemistry',
        'biochemistry dept' => 'Department of Biochemistry',

        // Department of Biotechnology
        'biotech' => 'Department of Biotechnology',
        'biotech dept' => 'Department of Biotechnology',

        // Department of Chemistry
        'chem' => 'Department of Chemistry',
        'chemistry' => 'Department of Chemistry',
        'chemistry dept' => 'Department of Chemistry',

        // Department of Civil Engineering
        'civil' => 'Department of Civil Engineering',
        'civil engg' => 'Department of Civil Engineering',
        'civil engineering dept' => 'Department of Civil Engineering',

        // Department of Commerce
        'comm' => 'Department of Commerce',
        'commerce dept' => 'Department of Commerce',

        // Department of Computer Science and Engineering
        'cse' => 'Department of Computer Science and Engineering',
        'cs' => 'Department of Computer Science and Engineering',
        'cs dept' => 'Department of Computer Science and Engineering',
        'computer science' => 'Department of Computer Science and Engineering',
        'cse dept' => 'Department of Computer Science and Engineering',

        // Department of Computer Sciences and Information Technology
        'csit' => 'Department of Computer Sciences and Information Technology',
        'cs it' => 'Department of Computer Sciences and Information Technology',

        // Department of Economics
        'econ' => 'Department of Economics',
        'economics dept' => 'Department of Economics',

        // Department of Electrical Engineering
        'ee' => 'Department of Electrical Engineering',
        'elec engg' => 'Department of Electrical Engineering',
        'electrical engg' => 'Department of Electrical Engineering',
        'electrical dept' => 'Department of Electrical Engineering',

        // Department of English and Foreign Languages
        'efl' => 'Department of English and Foreign Languages',
        'english dept' => 'Department of English and Foreign Languages',
        'english' => 'Department of English and Foreign Languages',

        // Department of Environmental Studies
        'env' => 'Department of Environmental Studies',
        'evs' => 'Department of Environmental Studies',
        'environmental studies' => 'Department of Environmental Studies',
        'environment' => 'Department of Environmental Studies',

        // Department of Geography
        'geog' => 'Department of Geography',
        'geography dept' => 'Department of Geography',

        // Department of Hindi
        'hindi' => 'Department of Hindi',
        'hindi dept' => 'Department of Hindi',

        // Department of History and Archaeology
        'hist' => 'Department of History and Archaeology',
        'history' => 'Department of History and Archaeology',
        'archaeology' => 'Department of History and Archaeology',

        // Department of Journalism and Mass Communication
        'jmc' => 'Department of Journalism and Mass Communication',
        'journalism' => 'Department of Journalism and Mass Communication',
        'mass comm' => 'Department of Journalism and Mass Communication',
        'mass communication' => 'Department of Journalism and Mass Communication',

        // Department of Law
        'law' => 'Department of Law',
        'law dept' => 'Department of Law',

        // Department of Library and Information Science
        'lis' => 'Department of Library and Information Science',
        'library science' => 'Department of Library and Information Science',

        // Department of Management Studies
        'mgmt' => 'Department of Management Studies',
        'management' => 'Department of Management Studies',
        'mba' => 'Department of Management Studies',

        // Department of Mathematics
        'math' => 'Department of Mathematics',
        'maths' => 'Department of Mathematics',
        'mathematics' => 'Department of Mathematics',
        'math dept' => 'Department of Mathematics',

        // Department of Microbiology
        'micro' => 'Department of Microbiology',
        'microbiology' => 'Department of Microbiology',

        // Department of Nutrition Biology
        'nutbio' => 'Department of Nutrition Biology',
        'nutrition biology' => 'Department of Nutrition Biology',

        // Department of Pharmaceutical Sciences
        'pharm' => 'Department of Pharmaceutical Sciences',
        'pharma' => 'Department of Pharmaceutical Sciences',
        'pharmacy' => 'Department of Pharmaceutical Sciences',

        // Department of Physical Education and Sports
        'pes' => 'Department of Physical Education and Sports',
        'physical education' => 'Department of Physical Education and Sports',
        'sports' => 'Department of Physical Education and Sports',
        'pes dept' => 'Department of Physical Education and Sports',

        // Department of Physics and Astrophysics
        'phyastro' => 'Department of Physics and Astrophysics',
        'physics' => 'Department of Physics and Astrophysics',
        'physics dept' => 'Department of Physics and Astrophysics',
        'astrophysics' => 'Department of Physics and Astrophysics',

        // Department of Political Science
        'pol sci' => 'Department of Political Science',
        'political science' => 'Department of Political Science',
        'pol science' => 'Department of Political Science',

        // Department of Printing and Packaging Technology
        'ppt' => 'Department of Printing and Packaging Technology',
        'printing' => 'Department of Printing and Packaging Technology',
        'packaging' => 'Department of Printing and Packaging Technology',

        // Department of Psychology
        'psy' => 'Department of Psychology',
        'psychology' => 'Department of Psychology',

        // Department of Sanskrit
        'sans' => 'Department of Sanskrit',
        'sanskrit' => 'Department of Sanskrit',

        // Department of Sociology
        'soc' => 'Department of Sociology',
        'sociology' => 'Department of Sociology',

        // Department of Statistics
        'stat' => 'Department of Statistics',
        'stats' => 'Department of Statistics',
        'statistics' => 'Department of Statistics',

        // Department of Tourism and Hotel Management
        'thm' => 'Department of Tourism and Hotel Management',
        'tourism' => 'Department of Tourism and Hotel Management',
        'hotel management' => 'Department of Tourism and Hotel Management',

        // Department of Yoga
        'yoga' => 'Department of Yoga',

        // Department of Vocational Studies and Skill Development
        'vssd' => 'Department of Vocational Studies and Skill Development',
        'vocational studies' => 'Department of Vocational Studies and Skill Development',
        'skill development' => 'Department of Vocational Studies and Skill Development',

        // Department of Teacher Education
        'teacher ed' => 'Department of Teacher Education',
        'teacher education' => 'Department of Teacher Education',
        'teacher training' => 'Department of Teacher Education',
        'teachered' => 'Department of Teacher Education',

        // Department of Biochemistry
        'biochem' => 'Department of Biochemistry',

        // Department of Chemistry
        'chem' => 'Department of Chemistry',

        // Department of Civil Engineering
        'civil engg' => 'Department of Civil Engineering',

        // Department of Computer Science and Engineering
        'cse' => 'Department of Computer Science and Engineering',

        // Department of Computer Sciences and Information Technology
        'csit' => 'Department of Computer Sciences and Information Technology',

        // Department of Economics
        'econ' => 'Department of Economics',

        // Department of Electrical Engineering
        'ee' => 'Department of Electrical Engineering',

        // Department of English and Foreign Languages
        'efl' => 'Department of English and Foreign Languages',

        // Department of Environmental Studies
        'env' => 'Department of Environmental Studies',

        // Department of Geography
        'geog' => 'Department of Geography',

        // Department of Hindi
        'hindi' => 'Department of Hindi',

        // Department of History and Archaeology
        'hist' => 'Department of History and Archaeology',

        // Department of Journalism and Mass Communication
        'jmc' => 'Department of Journalism and Mass Communication',

        // Department of Law
        'law' => 'Department of Law',

        // Department of Library and Information Science
        'lis' => 'Department of Library and Information Science',

        // Department of Management Studies
        'mgmt' => 'Department of Management Studies',

        // Department of Mathematics
        'math' => 'Department of Mathematics',

        // Department of Microbiology
        'micro' => 'Department of Microbiology',

        // Department of Nutrition Biology
        'nutbio' => 'Department of Nutrition Biology',

        // Department of Pharmaceutical Sciences
        'pharm' => 'Department of Pharmaceutical Sciences',

        // Department of Physical Education and Sports
        'pes' => 'Department of Physical Education and Sports',

        // Department of Physics and Astrophysics
        'phyastro' => 'Department of Physics and Astrophysics',

        // Department of Political Science
        'pol sci' => 'Department of Political Science',

        // Department of Printing and Packaging Technology
        'ppt' => 'Department of Printing and Packaging Technology',

        // Department of Psychology
        'psy' => 'Department of Psychology',

        // Department of Sanskrit
        'sans' => 'Department of Sanskrit',

        // Department of Sociology
        'soc' => 'Department of Sociology',

        // Department of Statistics
        'stat' => 'Department of Statistics',

        // Department of Tourism and Hotel Management
        'thm' => 'Department of Tourism and Hotel Management',

        // Department of Yoga
        'yoga' => 'Department of Yoga',

        // Department of Vocational Studies and Skill Development
        'vssd' => 'Department of Vocational Studies and Skill Development',

        // Department of Teacher Education
        'teacher ed' => 'Department of Teacher Education',

        // Schools of Studies
        'shss' => 'School of Humanities and Social Sciences',
        'school of humanities' => 'School of Humanities and Social Sciences',
        'humanities and social sciences' => 'School of Humanities and Social Sciences',

        'sias' => 'School of Interdisciplinary and Applied Sciences',
        'interdisciplinary' => 'School of Interdisciplinary and Applied Sciences',
        'interdisciplinary applied sciences' => 'School of Interdisciplinary and Applied Sciences',

        'sbs' => 'School of Basic Sciences',
        'basic sciences' => 'School of Basic Sciences',

        'sol' => 'School of Law',
        'school of law' => 'School of Law',
        'law school' => 'School of Law',

        'soet' => 'School of Engineering and Technology',
        'school of engineering' => 'School of Engineering and Technology',
        'engineering school' => 'School of Engineering and Technology',
        'school of technology' => 'School of Engineering and Technology',

        'sbms' => 'School of Business and Management Studies',
        'business school' => 'School of Business and Management Studies',
        'management school' => 'School of Business and Management Studies',

        'soe' => 'School of Education',
        'school of education' => 'School of Education',
        'education school' => 'School of Education',

        'slll' => 'School of Life-long Learning',
        'lifelong learning' => 'School of Life-long Learning',

        // Non-Teaching Offices and Administrative Branches
        'vcsec' => "Vice-Chancellor's Secretariat",
        'vice chancellor' => "Vice-Chancellor's Secretariat",
        'vc office' => "Vice-Chancellor's Secretariat",

        'pvc' => "Pro-Vice Chancellor's Office",
        'pro vice chancellor' => "Pro-Vice Chancellor's Office",
        'pvc office' => "Pro-Vice Chancellor's Office",

        'regoff' => 'Registrar Office',
        'registrar' => 'Registrar Office',
        'registrar office' => 'Registrar Office',

        'finance' => 'Finance Branch',
        'finance branch' => 'Finance Branch',
        'finance dept' => 'Finance Branch',

        'accounts' => 'Accounts Branch',
        'accounts branch' => 'Accounts Branch',
        'accounts dept' => 'Accounts Branch',

        'exam' => 'Examination Branch',
        'exam branch' => 'Examination Branch',
        'examination' => 'Examination Branch',
        'exam branch' => 'Examination Branch',

        'estb' => 'Establishment Branch',
        'establishment' => 'Establishment Branch',
        'establishment branch' => 'Establishment Branch',

        'ega' => 'Estate and General Administration Branch',
        'estate' => 'Estate and General Administration Branch',
        'estate admin' => 'Estate and General Administration Branch',

        'academic' => 'Academic and Council Branch',
        'academic branch' => 'Academic and Council Branch',
        'council branch' => 'Academic and Council Branch',

        'regsch' => 'Registration and Scholarship Branch',
        'registration' => 'Registration and Scholarship Branch',
        'scholarship' => 'Registration and Scholarship Branch',

        'purchase' => 'Purchase Branch',
        'purchase branch' => 'Purchase Branch',
        'procurement' => 'Purchase Branch',

        'store' => 'Store Branch',
        'store branch' => 'Store Branch',
        'stores' => 'Store Branch',

        'infra' => 'Infrastructure Branch',
        'infrastructure' => 'Infrastructure Branch',
        'infrastructure branch' => 'Infrastructure Branch',

        'engg' => 'Engineering and Maintenance Branch',
        'engineering' => 'Engineering and Maintenance Branch',
        'maintenance' => 'Engineering and Maintenance Branch',

        'pro' => 'Public Relations Office',
        'pr' => 'Public Relations Office',
        'public relations' => 'Public Relations Office',

        'proctor' => 'Proctor Office',
        'proctor office' => 'Proctor Office',

        'cvo' => 'Chief Vigilance Office',
        'vigilance' => 'Chief Vigilance Office',

        'security' => 'Security Office',
        'security office' => 'Security Office',

        'cc' => 'Computer Centre',
        'computer centre' => 'Computer Centre',
        'computer center' => 'Computer Centre',
        'cc dept' => 'Computer Centre',

        'ict' => 'ICT Section',
        'ict section' => 'ICT Section',
        'information technology' => 'ICT Section',

        'library' => 'Pandit Deendayal Upadhyaya Central Library',
        'lib' => 'Pandit Deendayal Upadhyaya Central Library',
        'central library' => 'Pandit Deendayal Upadhyaya Central Library',
        'library dept' => 'Pandit Deendayal Upadhyaya Central Library',

        'tpo' => 'Training and Placement Cell',
        'placement' => 'Training and Placement Cell',
        'placement cell' => 'Training and Placement Cell',

        'dsw' => 'Dean Student Welfare Office',
        'dean student welfare' => 'Dean Student Welfare Office',
        'student welfare' => 'Dean Student Welfare Office',

        'provb' => "Provost Office - Boys' Hostel",
        'boys hostel' => "Provost Office - Boys' Hostel",
        'boys hostel office' => "Provost Office - Boys' Hostel",

        'provg' => "Provost Office - Girls' Hostel",
        'girls hostel' => "Provost Office - Girls' Hostel",
        'girls hostel office' => "Provost Office - Girls' Hostel",

        'medical' => 'Medical Centre',
        'medical centre' => 'Medical Centre',
        'health centre' => 'Medical Centre',
        'health center' => 'Medical Centre',

        'rdc' => 'Research and Development Cell',
        'r&d' => 'Research and Development Cell',
        'research' => 'Research and Development Cell',

        'iqac' => 'Internal Quality Assurance Cell',
        'quality assurance' => 'Internal Quality Assurance Cell',

        'cii' => 'Centre for Innovation and Incubation',
        'innovation' => 'Centre for Innovation and Incubation',
        'incubation' => 'Centre for Innovation and Incubation',

        'cdoe' => 'Centre for Distance and Online Education',
        'distance education' => 'Centre for Distance and Online Education',
        'online education' => 'Centre for Distance and Online Education',

        'sports' => 'Sports Office',
        'sports office' => 'Sports Office',

        'rajbhasha' => 'Rajbhasha Office',
        'hindi office' => 'Rajbhasha Office',

        'legal' => 'Legal Section',
        'legal section' => 'Legal Section',
        'law section' => 'Legal Section',

        'rti' => 'RTI Cell',
        'rti cell' => 'RTI Cell',
        'right to information' => 'RTI Cell',

        'hostel' => 'Hostel Administration',
        'hostel admin' => 'Hostel Administration',

        'guesthouse' => 'Guest House Administration',
        'guest house' => 'Guest House Administration',
        'guest house admin' => 'Guest House Administration',

        'ncc' => 'NCC Office',
        'ncc office' => 'NCC Office',

        'nss' => 'National Service Scheme Cell',
        'nss cell' => 'National Service Scheme Cell',
        'national service scheme' => 'National Service Scheme Cell',
    ];

    /**
     * Category aliases - configurable mapping from Excel/item values to database categories.
     * Maps to umbrella (parent) categories for simplified classification.
     */
    protected array $categoryAliases = [
        // Computers & Peripherals
        'desktop' => 'Computers & Peripherals',
        'desktops' => 'Computers & Peripherals',
        'desktop computer' => 'Computers & Peripherals',
        'desktop computers' => 'Computers & Peripherals',
        'workstation' => 'Computers & Peripherals',
        'thin client' => 'Computers & Peripherals',
        'all-in-one' => 'Computers & Peripherals',
        'all in one' => 'Computers & Peripherals',
        'aio' => 'Computers & Peripherals',

        'laptop' => 'Computers & Peripherals',
        'laptops' => 'Computers & Peripherals',
        'notebook' => 'Computers & Peripherals',
        'notebooks' => 'Computers & Peripherals',

        'server' => 'Computers & Peripherals',
        'servers' => 'Computers & Peripherals',
        'rack server' => 'Computers & Peripherals',
        'tower server' => 'Computers & Peripherals',
        'blade server' => 'Computers & Peripherals',

        'monitor' => 'Computers & Peripherals',
        'monitors' => 'Computers & Peripherals',
        'led monitor' => 'Computers & Peripherals',
        'lcd monitor' => 'Computers & Peripherals',
        'display' => 'Computers & Peripherals',

        'keyboard' => 'Computers & Peripherals',
        'keyboards' => 'Computers & Peripherals',
        'mouse' => 'Computers & Peripherals',
        'mice' => 'Computers & Peripherals',
        'webcam' => 'Computers & Peripherals',
        'headset' => 'Computers & Peripherals',
        'headsets' => 'Computers & Peripherals',
        'headphone' => 'Computers & Peripherals',
        'headphones' => 'Computers & Peripherals',
        'speaker' => 'Computers & Peripherals',
        'speakers' => 'Computers & Peripherals',
        'microphone' => 'Computers & Peripherals',
        'mic' => 'Computers & Peripherals',
        // Note: speaker, microphone, headset also appear in Audio/Video but Computers & Peripherals takes precedence as computer peripherals
        'docking station' => 'Computers & Peripherals',
        'dock' => 'Computers & Peripherals',
        'kvm' => 'Computers & Peripherals',
        'kvm switch' => 'Computers & Peripherals',

        // Networking & Communication
        'switch' => 'Networking & Communication',
        'switches' => 'Networking & Communication',
        'network switch' => 'Networking & Communication',
        'poe switch' => 'Networking & Communication',

        'router' => 'Networking & Communication',
        'routers' => 'Networking & Communication',

        'firewall' => 'Networking & Communication',
        'load balancer' => 'Networking & Communication',

        'access point' => 'Networking & Communication',
        'access points' => 'Networking & Communication',
        'wireless ap' => 'Networking & Communication',
        'wireless access point' => 'Networking & Communication',

        'controller' => 'Networking & Communication',
        'wlc' => 'Networking & Communication',

        'conference phone' => 'Networking & Communication',
        'telephone' => 'Networking & Communication',
        'ip phone' => 'Networking & Communication',
        'voip phone' => 'Networking & Communication',

        // Cables & Accessories
        'cable' => 'Cables & Accessories',
        'cables' => 'Cables & Accessories',
        'power cable' => 'Cables & Accessories',
        'network cable' => 'Cables & Accessories',
        'ethernet cable' => 'Cables & Accessories',
        'usb cable' => 'Cables & Accessories',
        'hdmi cable' => 'Cables & Accessories',
        'vga cable' => 'Cables & Accessories',
        'adapter' => 'Cables & Accessories',
        'adapters' => 'Cables & Accessories',
        'connector' => 'Cables & Accessories',
        'connectors' => 'Cables & Accessories',
        'patch cord' => 'Cables & Accessories',
        'patch panel' => 'Cables & Accessories',

        // Printing & Scanning
        'printer' => 'Printing & Scanning',
        'printers' => 'Printing & Scanning',
        'laser printer' => 'Printing & Scanning',
        'inkjet printer' => 'Printing & Scanning',
        'multifunction printer' => 'Printing & Scanning',
        'mfp' => 'Printing & Scanning',

        'scanner' => 'Printing & Scanning',
        'scanners' => 'Printing & Scanning',

        'photocopier' => 'Printing & Scanning',
        'copier' => 'Printing & Scanning',
        'fax' => 'Printing & Scanning',
        'fax machine' => 'Printing & Scanning',
        'plotter' => 'Printing & Scanning',

        // Audio/Video & Presentation
        'projector' => 'Audio/Video & Presentation',
        'projectors' => 'Audio/Video & Presentation',

        'tv' => 'Audio/Video & Presentation',
        'television' => 'Audio/Video & Presentation',
        'smart tv' => 'Audio/Video & Presentation',
        'led tv' => 'Audio/Video & Presentation',
        'lcd tv' => 'Audio/Video & Presentation',
        'plasma' => 'Audio/Video & Presentation',

        'display panel' => 'Audio/Video & Presentation',
        'video wall' => 'Audio/Video & Presentation',
        'interactive panel' => 'Audio/Video & Presentation',
        'interactive board' => 'Audio/Video & Presentation',
        'smart board' => 'Audio/Video & Presentation',
        'whiteboard' => 'Audio/Video & Presentation',

        'audio equipment' => 'Audio/Video & Presentation',
        'audio' => 'Audio/Video & Presentation',
        'amplifier' => 'Audio/Video & Presentation',
        'mixer' => 'Audio/Video & Presentation',

        'virtual class' => 'Audio/Video & Presentation',
        'virtual classroom' => 'Audio/Video & Presentation',
        'smart class' => 'Audio/Video & Presentation',
        'smart classroom' => 'Audio/Video & Presentation',
        'digital class' => 'Audio/Video & Presentation',
        'digital classroom' => 'Audio/Video & Presentation',

        'video conferencing' => 'Audio/Video & Presentation',
        'vc' => 'Audio/Video & Presentation',
        'video conference' => 'Audio/Video & Presentation',
        'video conference set' => 'Audio/Video & Presentation',
        'polycom' => 'Audio/Video & Presentation',

        // Power & Backup
        'ups' => 'Power & Backup',
        'ups system' => 'Power & Backup',
        'battery backup' => 'Power & Backup',
        'uninterruptible power supply' => 'Power & Backup',

        'inverter' => 'Power & Backup',
        'stabilizer' => 'Power & Backup',
        'voltage stabilizer' => 'Power & Backup',

        'generator' => 'Power & Backup',
        'dg set' => 'Power & Backup',
        'diesel generator' => 'Power & Backup',

        'solar' => 'Power & Backup',
        'solar panel' => 'Power & Backup',
        'solar power' => 'Power & Backup',

        'battery' => 'Power & Backup',
        'batteries' => 'Power & Backup',

        // Furniture & Fixtures
        'chair' => 'Furniture & Fixtures',
        'chairs' => 'Furniture & Fixtures',
        'table' => 'Furniture & Fixtures',
        'tables' => 'Furniture & Fixtures',
        'desk' => 'Furniture & Fixtures',
        'desks' => 'Furniture & Fixtures',
        'cabinet' => 'Furniture & Fixtures',
        'cabinets' => 'Furniture & Fixtures',
        'rack' => 'Furniture & Fixtures',
        'racks' => 'Furniture & Fixtures',
        'network rack' => 'Furniture & Fixtures',
        'lecture stand' => 'Furniture & Fixtures',
        'podium' => 'Furniture & Fixtures',
        'workstation' => 'Furniture & Fixtures',
        'bench' => 'Furniture & Fixtures',
        'stool' => 'Furniture & Fixtures',
        'locker' => 'Furniture & Fixtures',
        'drawer' => 'Furniture & Fixtures',
        'drawers' => 'Furniture & Fixtures',
        'pedestal' => 'Furniture & Fixtures',
        'pedestals' => 'Furniture & Fixtures',
        'side unit' => 'Furniture & Fixtures',
        'side units' => 'Furniture & Fixtures',
        'filing cabinet' => 'Furniture & Fixtures',
        'cupboard' => 'Furniture & Fixtures',
        'shelf' => 'Furniture & Fixtures',
        'shelves' => 'Furniture & Fixtures',
        'bookcase' => 'Furniture & Fixtures',
        'wardrobe' => 'Furniture & Fixtures',

        // Appliances
        'ac' => 'Appliances',
        'air conditioner' => 'Appliances',
        'airconditioner' => 'Appliances',
        'ac unit' => 'Appliances',
        'split ac' => 'Appliances',
        'window ac' => 'Appliances',
        'cassette ac' => 'Appliances',
        'vrf' => 'Appliances',
        'chiller' => 'Appliances',
        'cooling' => 'Appliances',
        'hvac' => 'Appliances',

        'fan' => 'Appliances',
        'fans' => 'Appliances',
        'ceiling fan' => 'Appliances',
        'exhaust fan' => 'Appliances',

        'refrigerator' => 'Appliances',
        'fridge' => 'Appliances',
        'freezer' => 'Appliances',
        'deep freezer' => 'Appliances',
        'water cooler' => 'Appliances',
        'water dispenser' => 'Appliances',

        // Storage & Media
        'storage' => 'Storage & Media',
        'storage device' => 'Storage & Media',
        'nas' => 'Storage & Media',
        'san' => 'Storage & Media',
        'hdd' => 'Storage & Media',
        'ssd' => 'Storage & Media',
        'hard disk' => 'Storage & Media',
        'hard drive' => 'Storage & Media',
        'external drive' => 'Storage & Media',
        'external hdd' => 'Storage & Media',
        'pen drive' => 'Storage & Media',
        'pendrive' => 'Storage & Media',
        'usb drive' => 'Storage & Media',
        'flash drive' => 'Storage & Media',
        'thumb drive' => 'Storage & Media',
        'memory card' => 'Storage & Media',
        'sd card' => 'Storage & Media',

        // Other Equipment (for items that don't fit above)
        'cctv' => 'Other Equipment',
        'camera' => 'Other Equipment',
        'cameras' => 'Other Equipment',
        'ip camera' => 'Other Equipment',
        'dome camera' => 'Other Equipment',
        'bullet camera' => 'Other Equipment',
        'ptz camera' => 'Other Equipment',

        'nvr' => 'Other Equipment',
        'dvr' => 'Other Equipment',
        'video recorder' => 'Other Equipment',

        'access control' => 'Other Equipment',
        'biometric' => 'Other Equipment',
        'fingerprint' => 'Other Equipment',
        'card reader' => 'Other Equipment',

        'software' => 'Other Equipment',
        'license' => 'Other Equipment',
        'license key' => 'Other Equipment',
        'subscription' => 'Other Equipment',

        'mobile' => 'Other Equipment',
        'smartphone' => 'Other Equipment',
        'tablet' => 'Other Equipment',
        'ipad' => 'Other Equipment',
        'mobile device' => 'Other Equipment',

        'medical' => 'Other Equipment',
        'patient monitor' => 'Other Equipment',
        'ventilator' => 'Other Equipment',
        'defibrillator' => 'Other Equipment',
        'ecg' => 'Other Equipment',
        'ultrasound' => 'Other Equipment',
        'x-ray' => 'Other Equipment',
        'ct scan' => 'Other Equipment',
        'mri' => 'Other Equipment',

        'centrifuge' => 'Other Equipment',
        'microscope' => 'Other Equipment',
        'spectrophotometer' => 'Other Equipment',
        'autoclave' => 'Other Equipment',
        'incubator' => 'Other Equipment',
        'fridge' => 'Other Equipment',
        'freezer' => 'Other Equipment',
        'refrigerator' => 'Other Equipment',
        'deep freezer' => 'Other Equipment',
        'ice maker' => 'Other Equipment',
        'water bath' => 'Other Equipment',
        'shaker' => 'Other Equipment',
        'vortex' => 'Other Equipment',
        'pipette' => 'Other Equipment',
        'balance' => 'Other Equipment',
        'scale' => 'Other Equipment',
        'ph meter' => 'Other Equipment',
        'conductivity meter' => 'Other Equipment',
        'lab equipment' => 'Other Equipment',
        'screw driver' => 'Other Equipment',
        'screwdriver' => 'Other Equipment',
        'screw driver set' => 'Other Equipment',
        'tool' => 'Other Equipment',
        'tools' => 'Other Equipment',
        'tool set' => 'Other Equipment',
        'tool kit' => 'Other Equipment',
        'wrench' => 'Other Equipment',
        'spanner' => 'Other Equipment',
        'hammer' => 'Other Equipment',
        'plier' => 'Other Equipment',
        'pliers' => 'Other Equipment',
    ];

    /**
     * Physical location keywords mapping to predefined locations.
     */
    protected array $locationKeywords = [
        'Research Lab' => [
            'research lab',
            'research laboratory',
            'r&d lab',
            'r & d lab',
            'research centre',
            'research center',
        ],
        'Equipment Lab' => [
            'equipment lab',
            'equipment laboratory',
            'equip lab',
            'instrument lab',
            'instrument laboratory',
            'test lab',
            'testing lab',
            'testing laboratory',
            'calibration lab',
            'calibration laboratory',
            'measurement lab',
        ],
        'Computer Lab' => [
            'computer lab',
            'computer laboratory',
            'computing lab',
            'it lab',
            'it laboratory',
            'cyber lab',
            'digital lab',
            'programming lab',
            'coding lab',
            'computer centre',
            'computer center',
            'e-lab',
            'e-learning lab',
            'virtual lab',
        ],
        'Classroom' => [
            'classroom',
            'class room',
            'lecture room',
            'teaching room',
            'tutorial room',
            'seminar room',
            'class',
            'academic block',
            'teaching block',
        ],
        'Lecture Hall' => [
            'lecture hall',
            'lecture theatre',
            'lecture theater',
            'auditorium',
            'auditorium hall',
            'convention hall',
            'seminar hall',
            'conference hall',
            'assembly hall',
        ],
        'Office' => [
            'office',
            'officer room',
            'officer cabin',
            'cabin',
            'cubicle',
            'workspace',
            'workstation',
            'admin office',
            'administration office',
            'department office',
            'hod office',
            'head office',
            'principal office',
            'director office',
            'dean office',
            'faculty room',
            'staff room',
            'reception',
            'front office',
        ],
        'Store Room' => [
            'store room',
            'storeroom',
            'store',
            'storage room',
            'storage',
            'warehouse',
            'godown',
            'stock room',
            'stockroom',
            'inventory room',
            'material store',
            'issue store',
        ],
        'Workshop' => [
            'workshop',
            'work shop',
            'mechanical workshop',
            'electrical workshop',
            'carpentry workshop',
            'fitting workshop',
            'welding workshop',
            'machine shop',
            'machine workshop',
            'fabrication workshop',
            'production workshop',
        ],
        'Library' => [
            'library',
            'central library',
            'main library',
            'department library',
            'reference library',
            'e-library',
            'e library',
            'digital library',
            'knowledge centre',
            'knowledge center',
            'learning resource centre',
            'learning resource center',
            'reading room',
            'study hall',
        ],
        'Computer Lab' => [
            'computer centre',
            'computer center',
        ],
    ];

    /**
     * Location aliases - common variations that map to "Other" with custom location preserved.
     */
    protected array $otherLocationKeywords = [
        'corridor' => 'Corridor',
        'corridors' => 'Corridor',
        'hallway' => 'Corridor',
        'passage' => 'Corridor',
        'passageway' => 'Corridor',
        'entrance' => 'Entrance',
        'entrance hall' => 'Entrance Hall',
        'main entrance' => 'Main Entrance',
        'main gate' => 'Main Gate',
        'gate' => 'Gate',
        'reception' => 'Reception',
        'reception area' => 'Reception Area',
        'lobby' => 'Lobby',
        'waiting area' => 'Waiting Area',
        'waiting room' => 'Waiting Room',
        'server room' => 'Server Room',
        'serverroom' => 'Server Room',
        'datacenter' => 'Data Center',
        'data center' => 'Data Center',
        'server rack' => 'Server Rack',
        'rack room' => 'Server Room',
        'network room' => 'Network Room',
        'ups room' => 'UPS Room',
        'battery room' => 'Battery Room',
        'electrical room' => 'Electrical Room',
        'electrical panel' => 'Electrical Panel',
        'panel room' => 'Electrical Panel',
        'generator room' => 'Generator Room',
        'dg room' => 'Generator Room',
        'panel room' => 'Electrical Panel',
        'switchgear room' => 'Switchgear Room',
        'transformer room' => 'Transformer Room',
        'pump room' => 'Pump Room',
        'water tank' => 'Water Tank',
        'overhead tank' => 'Water Tank',
        'pump house' => 'Pump House',
        'canteen' => 'Canteen',
        'cafeteria' => 'Cafeteria',
        'mess' => 'Mess',
        'dining hall' => 'Dining Hall',
        'kitchen' => 'Kitchen',
        'pantry' => 'Pantry',
        'toilet' => 'Toilet',
        'restroom' => 'Toilet',
        'washroom' => 'Toilet',
        'bathroom' => 'Bathroom',
        'lift' => 'Lift',
        'elevator' => 'Elevator',
        'staircase' => 'Staircase',
        'stairs' => 'Staircase',
        'parking' => 'Parking',
        'parking lot' => 'Parking',
        'garage' => 'Garage',
        'basement' => 'Basement',
        'terrace' => 'Terrace',
        'roof' => 'Roof',
        'rooftop' => 'Rooftop',
        'balcony' => 'Balcony',
        'verandah' => 'Verandah',
        'garden' => 'Garden',
        'lawn' => 'Lawn',
        'playground' => 'Playground',
        'sports ground' => 'Sports Ground',
        'gym' => 'Gym',
        'gymnasium' => 'Gymnasium',
        'fitness centre' => 'Gym',
        'fitness center' => 'Gym',
        'yoga room' => 'Yoga Room',
        'meditation room' => 'Meditation Room',
        'prayer room' => 'Prayer Room',
        'meditation centre' => 'Meditation Room',
        'meditation center' => 'Meditation Room',
        'common room' => 'Common Room',
        'student lounge' => 'Student Lounge',
        'faculty lounge' => 'Faculty Lounge',
        'guest house' => 'Guest House',
        'guest room' => 'Guest Room',
        'hostel' => 'Hostel',
        'hostel room' => 'Hostel Room',
        'dormitory' => 'Dormitory',
        'residential' => 'Residential',
        'quarters' => 'Quarters',
        'staff quarters' => 'Staff Quarters',
        'faculty quarters' => 'Faculty Quarters',
        'block a' => 'Block A',
        'block b' => 'Block B',
        'block c' => 'Block C',
        'block d' => 'Block D',
        'block e' => 'Block E',
        'block f' => 'Block F',
        'block g' => 'Block G',
        'block h' => 'Block H',
        'block' => 'Block',
        'wing' => 'Wing',
        'floor' => 'Floor',
        'ground floor' => 'Ground Floor',
        'first floor' => 'First Floor',
        'second floor' => 'Second Floor',
        'third floor' => 'Third Floor',
        'basement' => 'Basement',
        'mezzanine' => 'Mezzanine',
        'terrace' => 'Terrace',
        'rooftop' => 'Rooftop',
        'portico' => 'Portico',
        'porch' => 'Porch',
        'verandah' => 'Verandah',
        'patio' => 'Patio',
        'court' => 'Court',
        'courtyard' => 'Courtyard',
        'atrium' => 'Atrium',
        'foyer' => 'Foyer',
        'vestibule' => 'Vestibule',
        'alcove' => 'Alcove',
        'niche' => 'Niche',
        'booth' => 'Booth',
        'kiosk' => 'Kiosk',
        'counter' => 'Counter',
        'desk' => 'Desk',
        'help desk' => 'Help Desk',
        'information desk' => 'Information Desk',
        'enquiry' => 'Enquiry',
        'enquiry counter' => 'Enquiry Counter',
        'registration' => 'Registration',
        'admission' => 'Admission',
        'examination' => 'Examination',
        'exam hall' => 'Exam Hall',
        'examination hall' => 'Exam Hall',
        'valuation' => 'Valuation',
        'valuation room' => 'Valuation Room',
        'committee room' => 'Committee Room',
        'board room' => 'Board Room',
        'boardroom' => 'Board Room',
        'meeting room' => 'Meeting Room',
        'conference room' => 'Conference Room',
        'discussion room' => 'Discussion Room',
        'brainstorming room' => 'Brainstorming Room',
        'training room' => 'Training Room',
        'training hall' => 'Training Hall',
        'seminar room' => 'Seminar Room',
        'webinar room' => 'Webinar Room',
        'video conference' => 'Video Conference',
        'vc room' => 'VC Room',
        'telepresence' => 'Telepresence',
        'studio' => 'Studio',
        'recording studio' => 'Recording Studio',
        'broadcast' => 'Broadcast',
        'control room' => 'Control Room',
        'monitoring' => 'Monitoring',
        'surveillance' => 'Surveillance',
        'security' => 'Security',
        'security room' => 'Security Room',
        'guard room' => 'Guard Room',
        'gate house' => 'Gate House',
        'watch tower' => 'Watch Tower',
        'boundary wall' => 'Boundary',
        'fence' => 'Fence',
        'gate' => 'Gate',
        'barrier' => 'Barrier',
        'boom barrier' => 'Boom Barrier',
        'turnstile' => 'Turnstile',
        'access control' => 'Access Control',
        'biometric' => 'Biometric',
        'cctv' => 'CCTV',
        'camera' => 'CCTV',
        'alarm' => 'Alarm',
        'fire alarm' => 'Fire Alarm',
        'smoke detector' => 'Smoke Detector',
        'sprinkler' => 'Sprinkler',
        'hydrant' => 'Hydrant',
        'fire extinguisher' => 'Fire Extinguisher',
        'first aid' => 'First Aid',
        'medical room' => 'Medical Room',
        'dispensary' => 'Dispensary',
        'clinic' => 'Clinic',
        'health centre' => 'Health Centre',
        'health center' => 'Health Center',
        'ambulance' => 'Ambulance',
        'emergency' => 'Emergency',
        'disaster' => 'Disaster Management',
        'evacuation' => 'Evacuation',
        'assembly point' => 'Assembly Point',
        'muster point' => 'Muster Point',
        'refuge' => 'Refuge',
        'shelter' => 'Shelter',
    ];

    /**
     * Room number patterns for extraction.
     */
    protected array $roomPatterns = [
        // Room 205, Room-205, R-205, R 205
        '/\b(?:room|rm|r)[\s\-]?(\d{2,4}[a-z]?)\b/i',
        // Lab 205, Lab-205, L-205
        '/\b(?:lab|l)[\s\-]?(\d{2,4}[a-z]?)\b/i',
        // Room No. 205, Room No 205, Room No. 205
        '/\b(?:room\s+no|room\s+no\.?|rm\s+no|rm\s+no\.?)[\s\.]*(\d{2,4}[a-z]?)\b/i',
        // Block A Room 205, Block-A-205
        '/\bblock\s*[a-z]\s*[\s\-]*(?:room|rm)?\s*(\d{2,4}[a-z]?)\b/i',
        // Floor 2 Room 205
        '/\bfloor\s*\d+[\s\-]*(?:room|rm)?\s*(\d{2,4}[a-z]?)\b/i',
        // Just a room number at end: "Physics 205", "Chemistry-301"
        '/^(.+?)[\s\-]+(\d{2,4}[a-z]?)\s*$/i',
        // Generic: any text followed by separator and 2-4 digits at end
        '/^(.+?)[\s\-]+(\d{2,4}[a-z]?)\s*$/i',
    ];

    /**
     * Match department from Excel value.
     *
     * @param  string  $excelValue  Raw department value from Excel
     * @return array{department: Department|null, status: string, matchType: string, original: string}
     */
    public function matchDepartment(string $excelValue): array
    {
        $original = $excelValue;
        $clean = $this->normalizeString($excelValue);

        if (empty($clean)) {
            return [
                'department' => null,
                'status' => 'empty',
                'matchType' => 'none',
                'original' => $original,
            ];
        }

        // 1. Try exact match on department name (case-insensitive)
        $exact = Department::query()
            ->where('status', 'active')
            ->get()
            ->first(fn ($d) => $this->normalizeString($d->name) === $clean);

        if ($exact) {
            return [
                'department' => $exact,
                'status' => 'matched',
                'matchType' => 'exact',
                'original' => $original,
            ];
        }

        // 2. Try exact match on department code
        $codeMatch = Department::query()
            ->where('status', 'active')
            ->where('code', strtoupper($clean))
            ->first();

        if ($codeMatch) {
            return [
                'department' => $codeMatch,
                'status' => 'matched',
                'matchType' => 'code',
                'original' => $original,
            ];
        }

        // 3. Check configured aliases (with punctuation-tolerant matching)
        $aliasKey = $this->normalizeForAliasMatch($excelValue);
        if (isset($this->departmentAliases[$aliasKey])) {
            $aliasTarget = $this->departmentAliases[$aliasKey];
            $aliasMatch = Department::query()
                ->where('status', 'active')
                ->get()
                ->first(fn ($d) => $this->normalizeString($d->name) === $this->normalizeString($aliasTarget));

            if ($aliasMatch) {
                return [
                    'department' => $aliasMatch,
                    'status' => 'matched',
                    'matchType' => 'alias',
                    'original' => $original,
                ];
            }
        }

        // 4. Normalized fuzzy match (normalized exact)
        $normalizedFuzzy = Department::query()
            ->where('status', 'active')
            ->get()
            ->first(fn ($d) => $this->normalizeString($d->name) === $clean);

        if ($normalizedFuzzy) {
            return [
                'department' => $normalizedFuzzy,
                'status' => 'matched',
                'matchType' => 'normalized',
                'original' => $original,
            ];
        }

        // 5. Partial match with high confidence - check if Excel value contains department name or vice versa
        $partial = Department::query()
            ->where('status', 'active')
            ->get()
            ->filter(function ($d) use ($clean) {
                $deptName = $this->normalizeString($d->name);

                return $deptName !== '' && ($clean === $deptName || str_contains($clean, $deptName) || str_contains($deptName, $clean));
            })
            ->first();

        if ($partial) {
            // Only accept if one is clearly contained in the other with good length
            $deptName = $this->normalizeString($partial->name);
            if (strlen($clean) > 3 && strlen($deptName) > 3 &&
                (str_contains($clean, $deptName) || str_contains($deptName, $clean))) {
                return [
                    'department' => $partial,
                    'status' => 'matched',
                    'matchType' => 'partial',
                    'original' => $original,
                ];
            }
        }

        return [
            'department' => null,
            'status' => 'unmatched',
            'matchType' => 'none',
            'original' => $original,
        ];
    }

    /**
     * Match category from item name.
     */
    public function matchCategory(string $itemName): array
    {
        $original = $itemName;
        $clean = $this->normalizeString($itemName);

        if (empty($clean)) {
            return [
                'category' => null,
                'status' => 'empty',
                'matchType' => 'none',
                'original' => $original,
            ];
        }

        // 1. Exact match
        $exact = Category::query()
            ->where('status', 'active')
            ->get()
            ->first(fn ($c) => $this->normalizeString($c->name) === $clean);

        if ($exact) {
            return [
                'category' => $exact,
                'status' => 'matched',
                'matchType' => 'exact',
                'original' => $original,
            ];
        }

        // 2. Check configured aliases
        if (isset($this->categoryAliases[$clean])) {
            $aliasTarget = $this->categoryAliases[$clean];
            $aliasMatch = Category::query()
                ->where('status', 'active')
                ->get()
                ->first(fn ($c) => $this->normalizeString($c->name) === $this->normalizeString($aliasTarget));

            if ($aliasMatch) {
                return [
                    'category' => $aliasMatch,
                    'status' => 'matched',
                    'matchType' => 'alias',
                    'original' => $original,
                ];
            }
        }

        // 3. Keyword-based matching from configured map
        // Sort aliases by key length (longest first) to prefer more specific matches
        $sortedAliases = $this->categoryAliases;
        uksort($sortedAliases, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($sortedAliases as $aliasKey => $canonicalName) {
            // Check if the alias key has keywords (nested array)
            if (is_array($canonicalName) && isset($canonicalName['keywords'])) {
                foreach ($canonicalName['keywords'] as $keyword) {
                    $normalizedKeyword = $this->normalizeName($keyword);
                    if ($normalizedKeyword !== '' && $this->containsWord($clean, $normalizedKeyword)) {
                        $category = Category::query()
                            ->where('status', 'active')
                            ->get()
                            ->first(fn ($c) => $this->normalizeString($c->name) === $this->normalizeString($canonicalName['canonical']));

                        if ($category) {
                            return [
                                'category' => $category,
                                'status' => 'matched',
                                'matchType' => 'keyword',
                                'original' => $original,
                            ];
                        }
                    }
                }
            } else {
                // Simple alias: check if alias key is contained as a word in item name
                $normalizedAlias = $this->normalizeName($aliasKey);
                if ($normalizedAlias !== '' && $this->containsWord($clean, $normalizedAlias)) {
                    $canonicalName = is_array($canonicalName) ? ($canonicalName['canonical'] ?? $aliasKey) : $canonicalName;
                    $category = Category::query()
                        ->where('status', 'active')
                        ->get()
                        ->first(fn ($c) => $this->normalizeString($c->name) === $this->normalizeString($canonicalName));

                    if ($category) {
                        return [
                            'category' => $category,
                            'status' => 'matched',
                            'matchType' => 'keyword',
                            'original' => $original,
                        ];
                    }
                }
            }
        }

        // 4. Partial match on existing category names
        $partial = Category::query()
            ->where('status', 'active')
            ->get()
            ->filter(fn ($c) => ($catName = $this->normalizeString($c->name)) !== '' && (str_contains($clean, $catName) || str_contains($catName, $clean)))
            ->first();

        if ($partial) {
            $catName = $this->normalizeString($partial->name);
            if (strlen($clean) > 3 && strlen($catName) > 3 &&
                (str_contains($clean, $catName) || str_contains($catName, $clean))) {
                return [
                    'category' => $partial,
                    'status' => 'matched',
                    'matchType' => 'partial',
                    'original' => $original,
                ];
            }
        }

        return [
            'category' => null,
            'status' => 'unmatched',
            'matchType' => 'none',
            'original' => $original,
        ];
    }

    /**
     * Match physical location from Excel text.
     */
    public function matchLocation(string $locationText): array
    {
        $original = $locationText;
        $clean = $this->normalizeString($locationText);

        if (empty($clean)) {
            return [
                'location' => null,
                'customLocation' => null,
                'status' => 'empty',
                'matchType' => 'none',
                'original' => $original,
            ];
        }

        // 1. Exact match with predefined locations
        foreach (self::PREDEFINED_LOCATIONS as $loc) {
            if ($this->normalizeString($loc) === $clean) {
                return [
                    'location' => $loc,
                    'customLocation' => null,
                    'status' => 'matched',
                    'matchType' => 'exact',
                    'original' => $original,
                ];
            }
        }

        // 2. Keyword-based matching for predefined locations
        foreach ($this->locationKeywords as $location => $keywords) {
            foreach ($keywords as $keyword) {
                $normalizedKeyword = $this->normalizeString($keyword);
                if ($normalizedKeyword !== '' && str_contains($clean, $normalizedKeyword)) {
                    return [
                        'location' => $location,
                        'customLocation' => null,
                        'status' => 'matched',
                        'matchType' => 'keyword',
                        'original' => $original,
                    ];
                }
            }
        }

        // 3. Check for "Other" location keywords
        foreach ($this->otherLocationKeywords as $keyword => $displayName) {
            $normalizedKeyword = $this->normalizeString($keyword);
            if ($normalizedKeyword !== '' && str_contains($clean, $normalizedKeyword)) {
                return [
                    'location' => 'Other',
                    'customLocation' => $displayName,
                    'status' => 'matched',
                    'matchType' => 'other_keyword',
                    'original' => $original,
                ];
            }
        }

        // 4. No match - use Other with original text as custom location
        return [
            'location' => 'Other',
            'customLocation' => $locationText,
            'status' => 'unmatched',
            'matchType' => 'fallback_other',
            'original' => $original,
        ];
    }

    /**
     * Extract room number from location text.
     */
    public function extractRoomNumber(string $locationText): ?string
    {
        $clean = trim($locationText);

        if (empty($clean)) {
            return null;
        }

        foreach ($this->roomPatterns as $pattern) {
            if (preg_match($pattern, $clean, $matches)) {
                // The room number is typically the last capture group
                $roomNumber = end($matches);
                if (! empty($roomNumber) && preg_match('/^\d{2,4}[a-z]?$/i', $roomNumber)) {
                    return strtoupper($roomNumber);
                }
            }
        }

        return null;
    }

    /**
     * Normalize string for comparison: lowercase, trim, remove extra spaces, remove punctuation.
     */
    public function normalizeString(string $value): string
    {
        $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = trim($value);

        return mb_strtolower($value);
    }

    /**
     * Normalize string for alias matching: lowercase, trim, remove extra spaces,
     * remove punctuation but preserve word boundaries (spaces).
     */
    public function normalizeForAliasMatch(string $value): string
    {
        $value = $this->normalizeString($value);
        // Remove punctuation but keep alphanumeric and spaces
        $value = preg_replace('/[^a-z0-9\s]/', '', $value);
        // Collapse multiple spaces again after punctuation removal
        $value = preg_replace('/\s+/', ' ', $value);
        return trim($value);
    }

    /**
     * Normalize name preserving meaningful case for display.
     */
    public function normalizeName(string $value): string
    {
        $value = $this->cleanOccupant($value);

        return $value;
    }

    /**
     * Check if a string contains a word (with word boundaries).
     */
    protected function containsWord(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }
        // Use word boundaries for more accurate matching
        $pattern = '/\b' . preg_quote($needle, '/') . '\b/';
        return (bool) preg_match($pattern, $haystack);
    }

    /**
     * Clean occupant/location text.
     */
    private function cleanOccupant(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                str_replace(
                    ["\r\n", "\r", "\n"],
                    ' ',
                    (string) $value
                )
            )
        );
    }

    /**
     * Normalize name for comparison (lowercase, no spaces/punctuation).
     */
    public function normalizeNameForComparison(string $value): string
    {
        $value = $this->normalizeString($value);
        // Remove common separators
        $value = preg_replace('/[^a-z0-9]/', '', $value);

        return $value;
    }

    /**
     * Get all configured department aliases.
     */
    public function getDepartmentAliases(): array
    {
        return $this->departmentAliases;
    }

    /**
     * Get all configured category aliases.
     */
    public function getCategoryAliases(): array
    {
        return $this->categoryAliases;
    }

    /**
     * Get all predefined locations.
     */
    public function getPredefinedLocations(): array
    {
        return self::PREDEFINED_LOCATIONS;
    }

    /**
     * Add custom department alias (for runtime configuration).
     */
    public function addDepartmentAlias(string $excelValue, string $targetDepartment): void
    {
        $this->departmentAliases[$this->normalizeString($excelValue)] = $targetDepartment;
    }

    /**
     * Add custom category alias.
     */
    public function addCategoryAlias(string $excelValue, string $targetCategory): void
    {
        $this->categoryAliases[$this->normalizeString($excelValue)] = $targetCategory;
    }
}
