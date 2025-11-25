 let currentQuestion = 0;
        let answers = {};

        const questions = [
            {
                id: 1,
                question: "¿Cuál es tu tipo de piel?",
                options: [
                    { value: "grasa", label: "Grasa (brillante, poros visibles)" },
                    { value: "seca", label: "Seca (tirante, descamación)" },
                    { value: "mixta", label: "Mixta (grasa en T, seca en mejillas)" },
                    { value: "sensible", label: "Sensible (se irrita fácilmente)" },
                    { value: "normal", label: "Normal (equilibrada)" }
                ]
            },
            {
                id: 2,
                question: "¿Cuáles son tus principales preocupaciones de la piel?",
                options: [
                    { value: "acne", label: "Acné y puntos negros" },
                    { value: "manchas", label: "Manchas y pigmentación" },
                    { value: "arrugas", label: "Líneas de expresión y arrugas" },
                    { value: "poros", label: "Poros dilatados" },
                    { value: "opacidad", label: "Falta de luminosidad" },
                    { value: "ninguna", label: "Ninguna preocupación específica" }
                ]
            },
            {
                id: 3,
                question: "¿Qué edad tienes?",
                options: [
                    { value: "18-25", label: "18-25 años" },
                    { value: "26-35", label: "26-35 años" },
                    { value: "36-45", label: "36-45 años" },
                    { value: "46+", label: "46 años o más" }
                ]
            },
            {
                id: 4,
                question: "¿Cuántos pasos incluye tu rutina actual?",
                options: [
                    { value: "ninguna", label: "No tengo rutina establecida" },
                    { value: "basica", label: "Básica (1-3 productos)" },
                    { value: "intermedia", label: "Intermedia (4-6 productos)" },
                    { value: "completa", label: "Completa (7+ productos)" }
                ]
            },
            {
                id: 5,
                question: "¿Con qué frecuencia te expones al sol?",
                options: [
                    { value: "diario", label: "Diariamente (trabajo/actividades al aire libre)" },
                    { value: "frecuente", label: "Frecuentemente (fines de semana)" },
                    { value: "ocasional", label: "Ocasionalmente" },
                    { value: "rara", label: "Rara vez" }
                ]
            },
            {
                id: 6,
                question: "¿Usas protector solar diariamente?",
                options: [
                    { value: "siempre", label: "Siempre, todos los días" },
                    { value: "dias-soleados", label: "Solo en días soleados" },
                    { value: "ocasional", label: "Ocasionalmente" },
                    { value: "nunca", label: "Nunca o casi nunca" }
                ]
            },
            {
                id: 7,
                question: "¿Cómo describirías tu estilo de vida?",
                options: [
                    { value: "activo", label: "Muy activo (ejercicio regular, deportes)" },
                    { value: "estresante", label: "Estresante (poco tiempo para cuidados)" },
                    { value: "equilibrado", label: "Equilibrado" },
                    { value: "relajado", label: "Relajado (tiempo para rutinas extensas)" }
                ]
            }
        ];

        function displayQuestion() {
            const question = questions[currentQuestion];
            const progressFill = document.getElementById('progress-fill');
            const currentQEl = document.getElementById('current-q');
            const questionTitle = document.getElementById('current-question-title');
            const questionText = document.getElementById('question-text');
            const optionsContainer = document.getElementById('options-container');

            // Update progress bar
            const progressPercent = ((currentQuestion + 1) / questions.length) * 100;
            progressFill.style.width = progressPercent + '%';
            
            // Update question counter
            currentQEl.textContent = currentQuestion + 1;
            
            // Update question text
            questionTitle.textContent = question.question;
            questionText.textContent = question.question;

            // Clear and populate options
            optionsContainer.innerHTML = '';
            question.options.forEach(option => {
                const optionButton = document.createElement('button');
                optionButton.className = 'option-button';
                optionButton.onclick = () => handleAnswer(option.value);
                
                optionButton.innerHTML = '<div class="option-text">' + option.label + '</div>';
                
                optionsContainer.appendChild(optionButton);
            });
        }

        function handleAnswer(value) {
            answers[currentQuestion] = value;
            
            if (currentQuestion < questions.length - 1) {
                currentQuestion++;
                setTimeout(() => {
                    displayQuestion();
                }, 200);
            } else {
                setTimeout(() => {
                    showResults();
                }, 200);
            }
        }

        function getRecommendations() {
            const skinType = answers[0];
            const concerns = answers[1];
            const age = answers[2];
            const routine = answers[3];
            const sunExposure = answers[4];
            const sunscreen = answers[5];
            const lifestyle = answers[6];

            let recommendations = {
                products: [],
                tips: [],
                routine: []
            };

            // Recomendaciones basadas en tipo de piel
            switch (skinType) {
                case 'grasa':
                    recommendations.products.push("Limpiador con ácido salicílico", "Tónico astringente", "Hidratante oil-free", "Mascarilla de arcilla");
                    recommendations.tips.push("Evita sobre-limpiar la piel", "Usa productos no comedogénicos");
                    break;
                case 'seca':
                    recommendations.products.push("Limpiador cremoso suave", "Sérum con ácido hialurónico", "Crema hidratante rica", "Aceite facial");
                    recommendations.tips.push("Usa agua tibia, no caliente", "Aplica hidratante en piel húmeda");
                    break;
                case 'mixta':
                    recommendations.products.push("Limpiador balanceado", "Tónico suave", "Hidratante ligero", "Mascarilla multizona");
                    recommendations.tips.push("Trata cada zona según sus necesidades", "Usa productos equilibrantes");
                    break;
                case 'sensible':
                    recommendations.products.push("Limpiador sin fragancia", "Productos hipoalergénicos", "Crema calmante", "Protector solar mineral");
                    recommendations.tips.push("Introduce productos nuevos gradualmente", "Evita fragancias y alcohol");
                    break;
                case 'normal':
                    recommendations.products.push("Limpiador suave", "Sérum antioxidante", "Hidratante equilibrante", "Exfoliante suave");
                    recommendations.tips.push("Mantén la rutina consistente", "Previene antes que tratar");
                    break;
            }

            // Recomendaciones por preocupaciones
            switch (concerns) {
                case 'acne':
                    recommendations.products.push("Tratamiento con retinol", "Spot treatment", "Exfoliante BHA");
                    recommendations.tips.push("No toques los granos", "Cambia fundas de almohada regularmente");
                    break;
                case 'manchas':
                    recommendations.products.push("Sérum con vitamina C", "Tratamiento despigmentante", "Exfoliante AHA");
                    recommendations.tips.push("Usa protector solar religiosamente", "Sé constante con el tratamiento");
                    break;
                case 'arrugas':
                    recommendations.products.push("Sérum con retinol", "Crema con péptidos", "Contorno de ojos");
                    recommendations.tips.push("Usa productos anti-edad por la noche", "Mantén la piel bien hidratada");
                    break;
            }

            // Rutina recomendada
            if (routine === 'ninguna' || routine === 'basica') {
                recommendations.routine = [
                    "Mañana: Limpiador → Hidratante → Protector solar",
                    "Noche: Limpiador → Tratamiento → Hidratante",
                    "Comienza con productos básicos y añade gradualmente"
                ];
            } else {
                recommendations.routine = [
                    "Mañana: Limpiador → Tónico → Sérum → Hidratante → Protector solar",
                    "Noche: Limpiador → Tónico → Tratamiento → Sérum → Hidratante",
                    "2-3 veces/semana: Exfoliante o mascarilla"
                ];
            }

            // Tips adicionales basados en estilo de vida
            if (lifestyle === 'estresante') {
                recommendations.tips.push("Simplifica tu rutina para que sea sostenible", "Prioriza limpieza e hidratación");
            }

            if (sunscreen !== 'siempre') {
                recommendations.tips.push("¡EL PROTECTOR SOLAR ES IMPRESCINDIBLE!", "Reaplica cada 2-3 horas si estás al aire libre");
            }

            return recommendations;
        }

        function showResults() {
            const quizContainer = document.getElementById('quiz-container');
            const resultsContainer = document.getElementById('results-container');
            const recs = getRecommendations();

            // Hide quiz, show results
            quizContainer.style.display = 'none';
            resultsContainer.style.display = 'block';

            // Populate products
            const productsList = document.getElementById('products-list');
            productsList.innerHTML = '';
            recs.products.forEach(product => {
                const li = document.createElement('li');
                li.className = 'recommendation-item';
                li.textContent = product;
                productsList.appendChild(li);
            });

            // Populate routine
            const routineList = document.getElementById('routine-list');
            routineList.innerHTML = '';
            recs.routine.forEach(step => {
                const li = document.createElement('li');
                li.className = 'recommendation-item';
                li.textContent = step;
                routineList.appendChild(li);
            });

            // Populate tips
            const tipsList = document.getElementById('tips-list');
            tipsList.innerHTML = '';
            recs.tips.forEach(tip => {
                const li = document.createElement('li');
                li.className = 'recommendation-item';
                li.textContent = tip;
                tipsList.appendChild(li);
            });
        }

        function resetQuiz() {
            currentQuestion = 0;
            answers = {};
            
            const quizContainer = document.getElementById('quiz-container');
            const resultsContainer = document.getElementById('results-container');
            
            quizContainer.style.display = 'block';
            resultsContainer.style.display = 'none';
            
            displayQuestion();
        }

        // Initialize the quiz
        document.addEventListener('DOMContentLoaded', function() {
            displayQuestion();
        });