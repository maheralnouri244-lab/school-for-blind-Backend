<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PastExam;
use App\Models\Question;
use App\Models\Subject;

class PastExamSeeder extends Seeder
{
    public function run(): void
    {
        $subject = Subject::firstOrCreate(
            ['name' => 'الكيمياء']
        );

        $teacherId = DB::table('teachers')->value('id') ?? 1;

        $examsList = [
            ['year' => '2021', 'session' => 'first', 'title' => 'امتحان شهادة الدراسة الثانوية - كيمياء - الدورة الأولى'],
            ['year' => '2021', 'session' => 'second', 'title' => 'امتحان شهادة الدراسة الثانوية - كيمياء - الدورة الثانية'],
            ['year' => '2022', 'session' => 'first', 'title' => 'الامتحان العام لنيل الشهادة الثانوية - كيمياء'],
            ['year' => '2023', 'session' => 'first', 'title' => 'امتحان بكالوريا علمي - مادة الكيمياء - دورة أولى'],
            ['year' => '2024', 'session' => 'first', 'title' => 'الامتحان الموحد للشهادة الثانوية العامة - الكيمياء'],
        ];

        $rawQuestions = [
            [
                'type' => 'mcq',
                'description' => 'من خاصيات أشعة غاما:',
                'correct_answer' => 'تنتشر بسرعة الضوء $c$',
                'choices' => [
                    ['text' => 'تتأثر بالحقل المغناطيسي', 'is_correct' => false],
                    ['text' => 'تتأثر بالحقل الكهربائي', 'is_correct' => false],
                    ['text' => 'تنتشر بسرعة الضوء $c$', 'is_correct' => true],
                    ['text' => 'تحمل شحنة سالبة', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'description' => 'في التفاعل المتوازن الآتي: $A_{(g)} + x B_{(g)} \rightleftarrows 3C_{(g)}$ يكون $K_p = K_c(RT)$ عندما تكون قيمة $x$ مساوية:',
                'correct_answer' => '2',
                'choices' => [
                    ['text' => '1', 'is_correct' => false],
                    ['text' => '2', 'is_correct' => true],
                    ['text' => '3', 'is_correct' => false],
                    ['text' => '4', 'is_correct' => false],
                ]
            ],
            [
                'type' => 'mcq',
                'description' => 'كل مادة كيميائية قادرة على منح زوج إلكتروني أو أكثر لمادة أخرى تتفاعل معها هي:',
                'correct_answer' => 'أساس لويس',
                'choices' => [
                    ['text' => 'حمض برونشتد - لوري', 'is_correct' => false],
                    ['text' => 'حمض لويس', 'is_correct' => false],
                    ['text' => 'أساس برونشتد - لوري', 'is_correct' => false],
                    ['text' => 'أساس لويس', 'is_correct' => true],
                ]
            ],
            [
                'type' => 'mcq',
                'description' => 'تزداد سرعة التفاعل الكيميائي بزيادة درجة الحرارة بسبب:',
                'correct_answer' => 'زيادة عدد الجزيئات التي تملك طاقة تنشيط',
                'choices' => [
                    ['text' => 'زيادة طاقة التنشيط نفسها', 'is_correct' => false],
                    ['text' => 'زيادة عدد الجزيئات التي تملك طاقة تنشيط', 'is_correct' => true],
                    ['text' => 'نقصان عدد التصادمات الفعالة', 'is_correct' => false],
                    ['text' => 'زيادة الحجم الكلي للمزيج', 'is_correct' => false],
                ]
            ],

            [
                'type' => 'TEXT',
                'description' => 'نعاير حمض النمل $HCOOH$ بهدروكسيد الصوديوم $NaOH$ والمطلوب: a) ما طبيعة الوسط عند نهاية المعايرة؟ ولماذا؟ b) حدّد المشعر المناسب لهذه المعايرة.',
                'correct_answer' => "a) الوسط أساسي، بسبب احتواء المحلول على أيونات النملات \$HCOO^-\$ الذي يسلك سلوك أساس (ضعيف).\nb) المشعر المناسب: فينول فتالئين."
            ],
            [
                'type' => 'TEXT',
                'description' => 'أكمل ووازن التفاعل النووي الآتي، ثم حدد نوع هذا التفاعل: $4 ^1_1H \longrightarrow ^4_2He + 2 ^0_{+1}e + \dots$',
                'correct_answer' => "المعادلة:\n\$\$4 ^1_1H \\longrightarrow ^4_2He + 2 ^0_{+1}e + Energy\$\$\nالنوع: إندماج نووي."
            ],
            [
                'type' => 'TEXT',
                'description' => 'يحدث التفاعل المتوازن الآتي في شروط مناسبة: $2NO_{(g)} + O_{2(g)} \rightleftarrows 2NO_{2(g)}$ ، $\Delta H < 0$. المطلوب: a) اكتب علاقة ثابت التوازن $K_p$ لهذا التفاعل المتوازن بدلالة الضغوط الجزئية. b) بيّن أثر زيادة درجة الحرارة على كلّ من: (حالة التوازن ، قيمة ثابت التوازن $K_c$).',
                'correct_answer' => "a) علاقة ثابت التوازن:\n\$\$K_p = \\frac{P^2_{(NO_2)}}{P^2_{(NO)} \\times P_{(O_2)}}\$\$\n\nb) أثر زيادة درجة الحرارة:\nيرجح التفاعل العكسي، وتنقص قيمة \$K_c\$."
            ],

            [
                'type' => 'TEXT',
                'description' => 'المسألة الأولى: يحوي وعاء مغلق حجمه 41 L مزيجاً غازياً مكوّن من 48 g من غاز الميتان $CH_4$ و 60 g من غاز الايتان $C_2H_6$. المطلوب حساب: الضغط الكلي للمزيج الغازي عند الدرجة 300 K. الكسر المولي لغاز الميتان عند درجة الحرارة السابقة.',
                'correct_answer' => "الطلب الأول (الضغط الكلي):\n\$n = \\frac{m}{M}\$\n\$n_{CH_4} = \\frac{48}{16} = 3 (mol)\$\n\$n_{C_2H_6} = \\frac{60}{30} = 2 (mol)\$\n\$P_t = \\frac{n_t R T}{V}\$\n\$P_t = \\frac{(3+2) \\times 0.082 \\times 300}{41}\$\n\$P_t = 3 \\text{ atm}\$\n\nالطلب الثاني (الكسر المولي):\n\$\$X_{(CH_4)} = \\frac{n_{CH_4}}{n_t}\$\$\n\$\$X_{(CH_4)} = \\frac{3}{5}\$\$"
            ],
            [
                'type' => 'TEXT',
                'description' => 'المسألة الثانية: يحدث التفاعل الأوّلي الآتي في شروط مناسبة: $2A_{(g)} + B_{(g)} \longrightarrow 2C_{(g)}$ فإذا علمت أنّ التراكيز الابتدائية: $[A]_0 = 0.4 mol.L^{-1}$ ، $[B]_0 = 0.2 mol.L^{-1}$ ، وثابت سرعة التفاعل $k = 10^{-2}$. المطلوب حساب السرعة الابتدائية للتفاعل وحدّد رتبته.',
                'correct_answer' => "الطلب الأول:\n\$v = k[A]^2[B]\$\n\$v_0 = 10^{-2}(0.4)^2(0.2)\$\n\$v_0 = 32 \\times 10^{-5} mol.L^{-1}.s^{-1}\$\nرتبة التفاعل: 3"
            ],
            [
                'type' => 'TEXT',
                'description' => 'المسألة الثالثة: تُذاب عينة غير نقية من هدروكسيد البوتاسيوم كتلتها 5.6 g في الماء المقطّر، ويُكمل الحجم إلى 800 mL، فإذا كان تركيز محلول هدروكسيد البوتاسيوم السابق $0.1 mol.L^{-1}$. المطلوب حساب قيمة $pH$ محلول هدروكسيد البوتاسيوم المستعمل.',
                'correct_answer' => "\$[KOH] = 10^{-1} mol.L^{-1}\$\n\$[OH^-] = 10^{-1} (mol.L^{-1})\$\n\$[H_3O^+] = 10^{-13} (mol.L^{-1})\$\n\$[H_3O^+] = 10^{-pH} \\Rightarrow pH = 13\$"
            ],
            [
                'type' => 'TEXT',
                'description' => 'المسألة الرابعة: محلول مائي مشبع لملح كلوريد الفضة $AgCl$ ، ذوبانيته $s = 2.5 \times 10^{-5} mol.L^{-1}$. المطلوب احسب قيمة ثابت جداء الذوبان $K_{sp(AgCl)}$ لهذا الملح.',
                'correct_answer' => "معادلة التوازن:\n\$\$AgCl_{(s)} \\rightleftarrows Ag^+_{(aq)} + Cl^-_{(aq)}\$\$\n\nالحساب:\n\$K_{sp} = [Ag^+][Cl^-]\$\n\$K_{sp} = (2.5 \\times 10^{-5})^2 = 6.25 \\times 10^{-10}\$"
            ]
        ];

        foreach ($examsList as $examData) {
            $selectedQuestions = collect($rawQuestions)->random(min(6, count($rawQuestions)));
            $pastExam = PastExam::create([
                'title' => $examData['title'],
                'subject_id' => $subject->id,
                'year' => $examData['year'],
                'session' => $examData['session'],
                'voice_solution_path' => null,
                'is_published' => true,
                'totalmark' => count($selectedQuestions),
                'numofquestions' => count($selectedQuestions),
                'timelimit' => 120,
            ]);


            foreach ($selectedQuestions as $index => $qData) {
                $prefix = "السؤال " . ($index + 1) . ": ";

                $question = Question::create([
                    'teacher_id' => $teacherId,
                    'type' => $qData['type'],
                    'status' => 'publish',
                    'description' => $prefix . $qData['description'],
                    'correct_answer' => $qData['correct_answer'],
                ]);

                $pastExam->questions()->attach($question->id);

                if ($qData['type'] === 'mcq' && isset($qData['choices'])) {
                    foreach ($qData['choices'] as $choice) {
                        $question->choices()->create([
                            'choice_text' => $choice['text'],
                            'is_correct' => $choice['is_correct'],
                        ]);
                    }
                }
            }
        }
    }
}