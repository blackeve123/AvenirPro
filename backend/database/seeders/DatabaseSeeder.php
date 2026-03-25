<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Question;
use App\Models\RiasecProfile;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin'],  ['display_name' => 'Administrator']);
        $userRole  = Role::firstOrCreate(['name' => 'user'],   ['display_name' => 'User']);

        // ── Users ─────────────────────────────────────────────────────────────
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@avenirpro.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'role_id' => $adminRole->id]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'student@avenirpro.com'],
            ['name' => 'Student User', 'password' => bcrypt('password'), 'role_id' => $userRole->id]
        );

        // ── RIASEC Profiles ───────────────────────────────────────────────────
        $profiles = [
            'R' => ['name' => 'Réaliste',       'description' => 'Pratique, physique, aime résoudre des problèmes manuels.'],
            'I' => ['name' => 'Investigateur',  'description' => 'Esprit analytique qui aime la recherche et l\'investigation.'],
            'A' => ['name' => 'Artistique',     'description' => 'Individu créatif et expressif.'],
            'S' => ['name' => 'Social',         'description' => 'Tourné vers les autres, aidant et communicant.'],
            'E' => ['name' => 'Entreprenant',   'description' => 'Leader ambitieux et persuasif.'],
            'C' => ['name' => 'Conventionnel',  'description' => 'Minutieux, organisé et structuré dans son travail.'],
        ];

        $riasecModels = [];
        foreach ($profiles as $code => $data) {
            $riasecModels[$code] = RiasecProfile::firstOrCreate(
                ['code' => $code],
                $data
            );
        }

        // ── Sample Questions ──────────────────────────────────────────────────
        $questionsData = [
            'R' => [
                'J\'aime travailler avec des outils et des machines.',
                'J\'aime construire ou réparer des objets.',
                'Les activités physiques en extérieur m\'intéressent.',
            ],
            'I' => [
                'J\'aime résoudre des problèmes scientifiques complexes.',
                'J\'aime analyser des données et en tirer des conclusions.',
                'La recherche et l\'expérimentation me passionnent.',
            ],
            'A' => [
                'J\'adore m\'exprimer à travers l\'art ou la musique.',
                'L\'écriture créative me vient naturellement.',
                'Je préfère les travaux qui font appel à l\'imagination et au design.',
            ],
            'S' => [
                'J\'aime enseigner et aider les autres.',
                'Je me sens épanoui(e) quand je soutiens les gens autour de moi.',
                'Travailler en équipe me motive.',
            ],
            'E' => [
                'J\'aime prendre les devants et diriger des projets.',
                'Convaincre les autres est quelque chose de naturel pour moi.',
                'Créer et diriger une entreprise m\'attire beaucoup.',
            ],
            'C' => [
                'Je préfère suivre des procédures et des règles claires.',
                'Organiser l\'information et les données me motive.',
                'L\'exactitude et la précision sont très importantes pour moi.',
            ],
        ];

        // Standard answer options (same for all questions)
        $answerOptions = [
            ['answer_text' => 'Pas du tout d\'accord', 'score' => 0],
            ['answer_text' => 'Plutôt pas d\'accord',   'score' => 1],
            ['answer_text' => 'Neutre',                'score' => 2],
            ['answer_text' => 'Plutôt d\'accord',       'score' => 3],
            ['answer_text' => 'Tout à fait d\'accord',  'score' => 4],
        ];

        $order = 1;
        foreach ($questionsData as $code => $texts) {
            foreach ($texts as $text) {
                $question = Question::firstOrCreate(
                    ['question_text' => $text],
                    [
                        'riasec_profile_id' => $riasecModels[$code]->id,
                        'order'             => $order++,
                    ]
                );
                if ($question->answers()->count() === 0) {
                    foreach ($answerOptions as $opt) {
                        Answer::create(array_merge($opt, ['question_id' => $question->id]));
                    }
                }
            }
        }

        // ── Job Categories ────────────────────────────────────────────────────
        $tech   = JobCategory::firstOrCreate(['name' => 'Informatique'],  ['description' => 'Métiers de l\'IT, du logiciel et du numérique.']);
        $health = JobCategory::firstOrCreate(['name' => 'Santé'],         ['description' => 'Professions médicales et du bien-être.']);
        $arts   = JobCategory::firstOrCreate(['name' => 'Arts et Création'], ['description' => 'Industries du design, des médias et des arts.']);
        $biz    = JobCategory::firstOrCreate(['name' => 'Commerce'],       ['description' => 'Management, finance et entrepreneuriat.']);

        // ── Sample Jobs ───────────────────────────────────────────────────────
        $jobs = [
            [
                'category_id'  => $tech->id,
                'title'        => 'Ingénieur Logiciel',
                'description'  => 'Conçoit, développe et maintient des applications logicielles.',
                'sector'       => 'Informatique',
                'salary_range' => '40 000 – 90 000 €',
                'riasec_types' => 'I,C',
            ],
            [
                'category_id'  => $tech->id,
                'title'        => 'Data Scientist',
                'description'  => 'Analyse de larges ensembles de données pour en extraire des insights métiers.',
                'sector'       => 'Informatique / Recherche',
                'salary_range' => '45 000 – 95 000 €',
                'riasec_types' => 'I,C',
            ],
            [
                'category_id'  => $tech->id,
                'title'        => 'Analyste Cybersécurité',
                'description'  => 'Protège les systèmes informatiques et les réseaux contre les menaces externes.',
                'sector'       => 'Informatique / Sécurité',
                'salary_range' => '40 000 – 85 000 €',
                'riasec_types' => 'I,R',
            ],
            [
                'category_id'  => $arts->id,
                'title'        => 'Designer UX/UI',
                'description'  => 'Crée des interfaces numériques belles et intuitives.',
                'sector'       => 'Design',
                'salary_range' => '35 000 – 70 000 €',
                'riasec_types' => 'A,I',
            ],
            [
                'category_id'  => $health->id,
                'title'        => 'Psychologue',
                'description'  => 'Aide les individus à comprendre et gérer leur santé mentale.',
                'sector'       => 'Santé',
                'salary_range' => '30 000 – 65 000 €',
                'riasec_types' => 'S,I',
            ],
            [
                'category_id'  => $biz->id,
                'title'        => 'Entrepreneur',
                'description'  => 'Lance et développe des projets d\'entreprise innovants.',
                'sector'       => 'Commerce',
                'salary_range' => 'Variable',
                'riasec_types' => 'E,I',
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::firstOrCreate(
                ['title' => $jobData['title']],
                $jobData
            );
        }

        // Call our specialized African Jobs Seeder
        $this->call(AfricanJobsSeeder::class);
    }
}
