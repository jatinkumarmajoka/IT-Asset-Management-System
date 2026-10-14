<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Teaching Departments
        $departments = [
            'Department of Applied Sciences and Humanities',
            'Department of Biochemistry',
            'Department of Biotechnology',
            'Department of Chemistry',
            'Department of Civil Engineering',
            'Department of Commerce',
            'Department of Computer Science and Engineering',
            'Department of Computer Sciences and Information Technology',
            'Department of Economics',
            'Department of Electrical Engineering',
            'Department of English and Foreign Languages',
            'Department of Environmental Studies',
            'Department of Geography',
            'Department of Hindi',
            'Department of History and Archaeology',
            'Department of Journalism and Mass Communication',
            'Department of Law',
            'Department of Library and Information Science',
            'Department of Management Studies',
            'Department of Mathematics',
            'Department of Microbiology',
            'Department of Nutrition Biology',
            'Department of Pharmaceutical Sciences',
            'Department of Physical Education and Sports',
            'Department of Physics and Astrophysics',
            'Department of Political Science',
            'Department of Printing and Packaging Technology',
            'Department of Psychology',
            'Department of Sanskrit',
            'Department of Sociology',
            'Department of Statistics',
            'Department of Tourism and Hotel Management',
            'Department of Yoga',
            'Department of Vocational Studies and Skill Development',
            'Department of Teacher Education',
        ];

        foreach ($departments as $name) {
            $code = match ($name) {
                'Department of Applied Sciences and Humanities' => 'ASH',
                'Department of Biochemistry' => 'BIOCHEM',
                'Department of Biotechnology' => 'BIOTECH',
                'Department of Chemistry' => 'CHEM',
                'Department of Civil Engineering' => 'CIVIL',
                'Department of Commerce' => 'COMM',
                'Department of Computer Science and Engineering' => 'CSE',
                'Department of Computer Sciences and Information Technology' => 'CSIT',
                'Department of Economics' => 'ECON',
                'Department of Electrical Engineering' => 'EE',
                'Department of English and Foreign Languages' => 'EFL',
                'Department of Environmental Studies' => 'ENV',
                'Department of Geography' => 'GEOG',
                'Department of Hindi' => 'HINDI',
                'Department of History and Archaeology' => 'HIST',
                'Department of Journalism and Mass Communication' => 'JMC',
                'Department of Law' => 'LAW',
                'Department of Library and Information Science' => 'LIS',
                'Department of Management Studies' => 'MGMT',
                'Department of Mathematics' => 'MATH',
                'Department of Microbiology' => 'MICRO',
                'Department of Nutrition Biology' => 'NUTBIO',
                'Department of Pharmaceutical Sciences' => 'PHARM',
                'Department of Physical Education and Sports' => 'PES',
                'Department of Physics and Astrophysics' => 'PHYASTRO',
                'Department of Political Science' => 'POLSCI',
                'Department of Printing and Packaging Technology' => 'PPT',
                'Department of Psychology' => 'PSY',
                'Department of Sanskrit' => 'SANS',
                'Department of Sociology' => 'SOC',
                'Department of Statistics' => 'STAT',
                'Department of Tourism and Hotel Management' => 'THM',
                'Department of Yoga' => 'YOGA',
                'Department of Vocational Studies and Skill Development' => 'VSSD',
                'Department of Teacher Education' => 'TEACHERED',
            };

            Department::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => null,
                    'status' => 'active',
                    'type' => 'academic',
                ]
            );
        }

        // Non-Teaching Offices and Administrative Branches
        // These entries represent offices/branches, not employees or posts.
        $nonTeachingDepartments = [
            ['name' => "Vice-Chancellor's Secretariat", 'code' => 'VCSEC'],
            ['name' => "Pro-Vice Chancellor's Office", 'code' => 'PVC'],
            ['name' => 'Registrar Office', 'code' => 'REGOFF'],
            ['name' => 'Finance Branch', 'code' => 'FINANCE'],
            ['name' => 'Accounts Branch', 'code' => 'ACCOUNTS'],
            ['name' => 'Examination Branch', 'code' => 'EXAM'],
            ['name' => 'Establishment Branch', 'code' => 'ESTB'],
            ['name' => 'Estate and General Administration Branch', 'code' => 'EGA'],
            ['name' => 'Academic and Council Branch', 'code' => 'ACADEMIC'],
            ['name' => 'Registration and Scholarship Branch', 'code' => 'REGSCH'],
            ['name' => 'Purchase Branch', 'code' => 'PURCHASE'],
            ['name' => 'Store Branch', 'code' => 'STORE'],
            ['name' => 'Infrastructure Branch', 'code' => 'INFRA'],
            ['name' => 'Engineering and Maintenance Branch', 'code' => 'ENGG'],
            ['name' => 'Public Relations Office', 'code' => 'PRO'],
            ['name' => 'Proctor Office', 'code' => 'PROCTOR'],
            ['name' => 'Chief Vigilance Office', 'code' => 'CVO'],
            ['name' => 'Security Office', 'code' => 'SECURITY'],
            ['name' => 'Computer Centre', 'code' => 'CC'],
            ['name' => 'ICT Section', 'code' => 'ICT'],
            ['name' => 'Pandit Deendayal Upadhyaya Central Library', 'code' => 'LIBRARY'],
            ['name' => 'Training and Placement Cell', 'code' => 'TPO'],
            ['name' => 'Dean Student Welfare Office', 'code' => 'DSW'],
            ['name' => "Provost Office - Boys' Hostel", 'code' => 'PROVB'],
            ['name' => "Provost Office - Girls' Hostel", 'code' => 'PROVG'],
            ['name' => 'Medical Centre', 'code' => 'MEDICAL'],
            ['name' => 'Research and Development Cell', 'code' => 'RDC'],
            ['name' => 'Internal Quality Assurance Cell', 'code' => 'IQAC'],
            ['name' => 'Centre for Innovation and Incubation', 'code' => 'CII'],
            ['name' => 'Centre for Distance and Online Education', 'code' => 'CDOE'],
            ['name' => 'Sports Office', 'code' => 'SPORTS'],
            ['name' => 'Rajbhasha Office', 'code' => 'RAJBHASHA'],
            ['name' => 'Legal Section', 'code' => 'LEGAL'],
            ['name' => 'RTI Cell', 'code' => 'RTI'],
            ['name' => 'Hostel Administration', 'code' => 'HOSTEL'],
            ['name' => 'Guest House Administration', 'code' => 'GUESTHOUSE'],
            ['name' => 'NCC Office', 'code' => 'NCC'],
            ['name' => 'National Service Scheme Cell', 'code' => 'NSS'],
        ];

        foreach ($nonTeachingDepartments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'description' => null,
                    'status' => 'active',
                    'type' => 'non_teaching',
                ]
            );
        }
    }
}
