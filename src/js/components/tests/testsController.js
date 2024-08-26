// / Générer auto par chatGPT, à vérifier et à modifier

class RegressionTestManager {
    constructor() {
        this.tests = [];  // Stocke tous les tests de régression
    }

    // Méthode pour ajouter un test de régression
    addTest(testName, testFunction) {
        this.tests.push({ name: testName, func: testFunction });
    }

    // Méthode pour exécuter tous les tests de régression
    runTests() {
        console.log("Running regression tests...");
        let passed = 0;
        let failed = 0;

        this.tests.forEach(test => {
            try {
                test.func();
                console.log(`✓ ${test.name} passed`);
                passed++;
            } catch (error) {
                console.error(`✗ ${test.name} failed`);
                console.error(error);
                failed++;
            }
        });

        console.log(`\nSummary: ${passed} passed, ${failed} failed`);
        return { passed, failed };
    }
}

// Exemple d'utilisation
const regressionTests = new RegressionTestManager();

// Ajouter des tests
regressionTests.addTest("Test d'une fonctionnalité ancienne", () => {
    const result = someFunction();
    if (result !== expectedValue) {
        throw new Error("Test échoué : le résultat n'est pas celui attendu.");
    }
});

regressionTests.addTest("Test d'une autre fonctionnalité", () => {
    const result = anotherFunction();
    if (result !== anotherExpectedValue) {
        throw new Error("Test échoué : le résultat n'est pas celui attendu.");
    }
});

// Exécuter les tests
regressionTests.runTests();
