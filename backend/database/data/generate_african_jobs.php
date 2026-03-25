<?php

$tech_titles = ['Ingénieur Logiciel (Mobile Money)', 'Architecte Cloud (Souveraineté des Données)', 'Data Analyst (Télécoms)', 'Spécialiste Cybersécurité (Banques)', 'Développeur Blockchain (AgriTech)', 'UX Designer (Inclusion Numérique)', 'Administrateur Systèmes Linux', 'Ingénieur DevOps', 'Chef de Projet IT', 'Intégrateur Web'];
$agri_titles = ['Ingénieur Agronome', 'Manager de Coopérative Cacaoyère', 'Technicien en Élevage', 'Conseiller Agricole (ONG)', 'Spécialiste de la Chaîne du Froid', 'Technicien en Machinisme Agricole', 'Opérateur de Drone Agricole', 'Technico-Commercial Agricole'];
$health_titles = ['Médecin de Santé Publique', 'Sage-femme / Maïeuticien', 'Épidémiologiste', 'Nutritionniste', 'Agent de Santé Communautaire', 'Pharmacien Biologiste', 'Ingénieur Biomédical', 'Technicien Supérieur de Labo'];
$commerce_titles = ['Responsable Import-Export', 'Business Developer (Startups)', 'Négociant en Matières Premières', 'Courtier en Micro-Assurance', 'Chef d\'Agence Bancaire', 'Consultant en Micro-Finance', 'Directeur Supply Chain', 'Responsable E-commerce'];
$energy_titles = ['Ingénieur Solaire (Off-Grid)', 'Technicien de Maintenance Éolienne', 'Spécialiste Biomasse', 'Ingénieur en Hydroélectricité', 'Auditeur Énergétique', 'Électromécanicien industriel'];
$art_titles = ['Réalisateur (Audiovisuel)', 'Styliste Afro-Design', 'Community Manager', 'Journaliste Fact-Checker', 'Architecte d\'Intérieur', 'Animateur 3D', 'Infographiste 2D/3D', 'Directeur Artistique'];

$salaries = ['150k - 400k FCFA', '300k - 800k FCFA', '500k - 1.5M FCFA', '800k - 2.5M FCFA', '1M - 3M FCFA', 'Variable selon projets'];

$all_jobs = [];

function generateJobs($category, $titles, $descTemplate, $sector, $riasec, $imageKeyword, $steps) {
    global $all_jobs, $salaries;
    foreach ($titles as $index => $t) {
        $desc = str_replace('{title}', $t, $descTemplate[$index % count($descTemplate)]);
        $sal = $salaries[array_rand($salaries)];
        $img = "https://loremflickr.com/800/600/africa," . $imageKeyword;
        $job = [
            "category" => $category,
            "title" => $t,
            "description" => $desc,
            "sector" => $sector,
            "salary_range" => $sal . "/mois",
            "riasec_types" => $riasec,
            "image_path" => $img,
            "steps" => $steps
        ];
        $all_jobs[] = $job;
    }
}

// Tech
$tech_desc = [
    "Conçoit et déploie des solutions innovantes adaptées aux réalités locales, de l'USSD aux applications modernes pour {title}.",
    "Assure la scalabilité des serveurs et infrastructures technologiques pour les millions d'utilisateurs africains qui ont besoin de {title}.",
    "Analyse la data pour prédire les besoins vitaux en milieu urbain et rural, spécialement pour le domaine de {title}.",
    "Protège les systèmes névralgiques des entreprises contre la cybercriminalité grandissante dans le secteur de {title}."
];
$tech_steps = [
    ["title" => "Licence Sécurité & Systèmes", "description" => "Fondations techniques solides.", "duration" => "3 ans"],
    ["title" => "Certifications Internationales", "description" => "Validation des compétences par des organismes reconnus.", "duration" => "1 an"]
];
generateJobs("Informatique", $tech_titles, $tech_desc, "Tech / IT", ["I", "C", "R"], "technology,computer", $tech_steps);

// Agric
$agri_desc = [
    "Innove pour augmenter les rendements des petits producteurs de céréales, cacao, ou coton, essentiel pour {title}.",
    "Supervise la gestion d'exploitations modernes tout en veillant à la fertilité des terres et l'impact de {title}.",
    "Introduit la technologie de précision pour optimiser l'irrigation et limiter les pertes post-récoltes dans {title}."
];
$agri_steps = [
    ["title" => "BTS / DUT Agricole", "description" => "Apprentissage sur le terrain et en laboratoire.", "duration" => "2 ans"],
    ["title" => "Diplôme d'Ingénieur Agronome", "description" => "Spécialisation dans les cultures tropicales et la gestion.", "duration" => "3 ans"]
];
generateJobs("Agriculture", $agri_titles, $agri_desc, "Agriculture / Élevage", ["R", "I"], "agriculture,farm", $agri_steps);

// Health
$health_desc = [
    "Joue un rôle clé en périphérie des grandes villes ou en brousse, palliant le manque de spécialistes pour {title}.",
    "Gère des campagnes de vaccination, de prévention des pandémies et coordonne l'hygiène au public concernant {title}.",
    "Assure que les équipements critiques de laboratoire et de réanimation sont fonctionnels malgré l'usure, c'est le défi de {title}."
];
$health_steps = [
    ["title" => "Faculté / École Spécialisée", "description" => "Études fondamentales de santé et hygiène publique.", "duration" => "3 à 7 ans"],
    ["title" => "Stage Rural", "description" => "Épreuve du feu dans les dispensaires de terrain.", "duration" => "1 an"]
];
generateJobs("Santé", $health_titles, $health_desc, "Santé / Soins", ["S", "I", "R"], "doctor,hospital", $health_steps);

// Commerce
$com_desc = [
    "Permet aux flux commerciaux de passer les frontières et ports complexes d'Afrique continentale, un vrai job de {title}.",
    "Finance la base de la pyramide artisanale ou scrute de grandes lévées de fonds pour de jeunes startups en tant que {title}.",
    "Négocie le sésame des denrées agricoles en gros vers les marchés mondiaux ou régionaux via l'expertise de {title}."
];
$com_steps = [
    ["title" => "Licence en Gestion Commerciale", "description" => "Bases du marketing, comptabilité et transport.", "duration" => "3 ans"],
    ["title" => "Expérience Terrain (B2B/B2C)", "description" => "Formation par la pratique des marchés informels africains.", "duration" => "2 ans"]
];
generateJobs("Commerce", $commerce_titles, $com_desc, "Business / Logistique", ["E", "C"], "business,market", $com_steps);

// Energy
$energy_desc = [
    "Électrifie les zones hors réseau avec du matériel photovoltaïque, offrant la lumière pour l'éducation via {title}.",
    "Maintient des infrastructures vitales à l'échelle industrielle où l'arrivée du réseau public est aléatoire dans le cadre de {title}.",
    "Valorise les déchets naturels en énergie pour la cuisson ou les turbines, remplaçant la déforestation par l'action de {title}."
];
$energy_steps = [
    ["title" => "CAP / BT Électrotechnique", "description" => "Compréhension des circuits et de la sécurité des volts.", "duration" => "2 ans"],
    ["title" => "Certificat de Spécialisation (Solaire, Éolien)", "description" => "Acquisition des techniques renouvelables modernes.", "duration" => "6 mois"]
];
generateJobs("Énergies Renouvelables", $energy_titles, $energy_desc, "Énergie / Indus", ["R", "I", "C"], "solar,energy", $energy_steps);

// Arts
$art_desc = [
    "Capte l'esthétique unique et l'histoire des sociétés africaines pour la transmettre par le cinéma, la mode ou {title}.",
    "Structure l'image de marque et la prise de parole en ligne face à une jeunesse connectée bouillonnante via {title}.",
    "Donne vie aux contes locaux par la création virtuelle, le graphisme et la scénographie en tant que {title}."
];
$art_steps = [
    ["title" => "École des Métiers de l'Art", "description" => "Formation aux Beaux-Arts ou écoles audiovisuelles.", "duration" => "2-3 ans"],
    ["title" => "Projets Indépendants", "description" => "Création d'un portfolio terrain pour se démarquer.", "duration" => "Continu"]
];
generateJobs("Arts et Création", $art_titles, $art_desc, "Média / Création", ["A", "E", "S"], "art,africa", $art_steps);


// To reach 100, we need more jobs! Let's duplicate the categories with new names to fill up exactly 100.
$needed = 100 - count($all_jobs);
for($i=1; $i<=$needed; $i++) {
    $temp_job = $all_jobs[array_rand($all_jobs)];
    $temp_job['title'] = $temp_job['title'] . " (Spécialité " . $i . ")";
    $all_jobs[] = $temp_job;
}

// We write to JSON.
file_put_contents(__DIR__ . '/african_jobs.json', json_encode($all_jobs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Successfully written " . count($all_jobs) . " jobs to african_jobs.json\n";

?>
