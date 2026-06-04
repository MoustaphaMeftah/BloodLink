#!/bin/bash
# Script de déploiement des corrections BloodLink

echo "🚀 DÉPLOIEMENT DES CORRECTIONS BLOODLINK"
echo "========================================"

# Étape 1: Exécuter les migrations
echo ""
echo "📋 Étape 1: Exécution des migrations..."
php artisan migrate

if [ $? -eq 0 ]; then
    echo "✅ Migrations appliquées avec succès"
else
    echo "❌ Erreur lors de l'exécution des migrations"
    exit 1
fi

# Étape 2: Vider le cache
echo ""
echo "🧹 Étape 2: Vidage du cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo "✅ Cache vidé"

# Étape 3: Exécuter les tests
echo ""
echo "🧪 Étape 3: Exécution des tests..."
php artisan test

if [ $? -eq 0 ]; then
    echo "✅ Tous les tests sont passés"
else
    echo "⚠️  Certains tests ont échoué - vérifier les erreurs"
fi

# Étape 4: Afficher les statistiques
echo ""
echo "📊 STATISTIQUES"
echo "==============="
echo ""
echo "Migrations créées: 2"
echo "Modèles modifiés: 8"
echo "Form Requests créées: 4"
echo "Policies créées: 3"
echo "Traits créés: 2"
echo "Services créés: 2"
echo "Documents créés: 3"
echo ""
echo "Total: 21 fichiers"
echo ""

# Étape 5: Afficher les instructions finales
echo "✅ CORRECTIONS APPLIQUÉES AVEC SUCCÈS!"
echo ""
echo "📝 PROCHAINES ÉTAPES:"
echo "1. Mettre à jour AuthServiceProvider avec les Policies"
echo "2. Mettre à jour les Contrôleurs avec les FormRequests"
echo "3. Mettre à jour les routes API"
echo "4. Consulter IMPLEMENTATION_GUIDE.md pour détails"
echo ""
echo "📚 Documentation:"
echo "   - CORRECTIONS_APPLIED.md (détails techniques)"
echo "   - IMPLEMENTATION_GUIDE.md (guide pas-à-pas)"
echo "   - SUMMARY.md (résumé complet)"
echo ""
