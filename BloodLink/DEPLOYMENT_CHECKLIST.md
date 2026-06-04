# ✅ CHECKLIST DE DÉPLOIEMENT - BLOODLINK CORRECTIONS

## 📋 PRÉ-DÉPLOIEMENT (AVANT DE COMMENCER)

- [ ] Backup de la base de données existante
- [ ] Backup du code actuel
- [ ] Test en environnement local confirmé
- [ ] Tous les développeurs notifiés du déploiement
- [ ] Git branch créée: `feature/corrections-bloodlink`
- [ ] Pull request créée (optionnel)

---

## 🔧 ÉTAPE 1: MIGRATIONS

```bash
# Tester les migrations d'abord
php artisan migrate --dry-run

# Exécuter les migrations
php artisan migrate

# Vérifier la base de données
php artisan migrate:status
```

**Migrations à appliquer:**
- [x] 2026_06_04_000000_add_missing_columns_to_users.php
- [x] 2026_06_04_000001_add_missing_columns_to_donors.php

**Colonnes ajoutées à `users`:**
- [ ] `verification_code` (nullable string)
- [ ] `password_reset_token` (nullable string)
- [ ] `last_login` (nullable timestamp)

**Colonnes ajoutées à `donors`:**
- [ ] `latitude` (nullable decimal 10,8)
- [ ] `longitude` (nullable decimal 11,8)
- [ ] `contact_verified` (boolean, default false)

---

## 🔒 ÉTAPE 2: ENREGISTRER LES POLICIES

**Fichier:** `app/Providers/AuthServiceProvider.php`

```php
protected $policies = [
    Donor::class => DonorPolicy::class,
    BloodRequest::class => BloodRequestPolicy::class,
    Message::class => MessagePolicy::class,
];
```

- [ ] DonorPolicy enregistrée
- [ ] BloodRequestPolicy enregistrée
- [ ] MessagePolicy enregistrée

---

## 🎯 ÉTAPE 3: METTRE À JOUR LES CONTRÔLEURS

### DonorController

```php
// Ajouter le trait
use App\Traits\ApiResponse;

// Mettre à jour les méthodes
public function store(UpdateDonorRequest $request): JsonResponse
{
    // Validation automatique du FormRequest
    $donor = Donor::create($request->validated());
    return $this->createdResponse($donor);
}

public function update(UpdateDonorRequest $request, Donor $donor): JsonResponse
{
    $this->authorize('update', $donor); // Policy check
    $donor->update($request->validated());
    return $this->successResponse($donor);
}
```

- [ ] DonorController mis à jour
- [ ] Utilise UpdateDonorRequest
- [ ] Utilise DonorPolicy
- [ ] Utilise ApiResponse trait

### DonationController

```php
// Ajouter le service
use App\Services\DonationService;
use App\Http\Requests\DonationStoreRequest;

public function __construct(protected DonationService $donationService) {}

public function store(DonationStoreRequest $request): JsonResponse
{
    try {
        $donation = $this->donationService->recordDonation($request->validated());
        return $this->createdResponse($donation->load('donor', 'bloodRequest'));
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

- [ ] DonationController mis à jour
- [ ] Utilise DonationStoreRequest
- [ ] Utilise DonationService
- [ ] Gère les exceptions correctement

### BloodRequestController

```php
// Ajouter le service
use App\Services\BloodRequestService;
use App\Http\Requests\BloodRequestStoreRequest;

public function __construct(protected BloodRequestService $bloodRequestService) {}

public function store(BloodRequestStoreRequest $request): JsonResponse
{
    $this->authorize('create', BloodRequest::class);
    try {
        $bloodRequest = BloodRequest::create($request->validated());
        $notified = $this->bloodRequestService->notifyCompatibleDonors($bloodRequest);
        return $this->createdResponse(
            ['blood_request' => $bloodRequest, 'donors_notified' => $notified]
        );
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

- [ ] BloodRequestController mis à jour
- [ ] Utilise BloodRequestStoreRequest
- [ ] Utilise BloodRequestService
- [ ] Notifie les donneurs

### MessageController

```php
use App\Traits\ApiResponse;
use App\Policies\MessagePolicy;

public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'content' => 'required|string|max:1000',
    ]);

    $message = Message::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $validated['receiver_id'],
        'content' => $validated['content'],
    ]);

    return $this->createdResponse($message);
}
```

- [ ] MessageController mis à jour
- [ ] Utilise ApiResponse trait
- [ ] Valide le contenu

---

## 🔐 ÉTAPE 4: METTRE À JOUR LES ROUTES

**Fichier:** `routes/api.php`

```php
Route::middleware('auth:sanctum')->group(function () {
    // Donors
    Route::apiResource('donors', DonorController::class);
    Route::get('donors/{donor}/history', [DonorController::class, 'history']);
    
    // Donations
    Route::apiResource('donations', DonationController::class)->only(['store', 'index']);
    
    // Blood Requests
    Route::apiResource('blood-requests', BloodRequestController::class);
    Route::get('blood-requests/{bloodRequest}/compatible-donors', [BloodRequestController::class, 'compatibleDonors']);
    
    // Messages
    Route::apiResource('messages', MessageController::class)->only(['store', 'index', 'destroy']);
});
```

- [ ] Routes donor correctes
- [ ] Routes donation correctes
- [ ] Routes blood-request correctes
- [ ] Routes messages correctes

---

## 🧪 ÉTAPE 5: TESTER LES VALIDATIONS

### Test RegisterRequest
```bash
# Tester un email invalide
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"invalid"}'
# Devrait retourner erreur de validation
```

- [ ] Email unique validé
- [ ] Password fort vérifié
- [ ] Blood type valide pour donneurs
- [ ] Phone format correct

### Test DonationStoreRequest
```bash
# Tester une quantité invalide
curl -X POST http://localhost:8000/api/donations \
  -H "Authorization: Bearer TOKEN" \
  -d '{"donor_id":1, "quantity":1000}'
# Devrait retourner erreur (max 500)
```

- [ ] Donor existe
- [ ] Quantity entre 100-500ml
- [ ] Date pas dans le futur

### Test UpdateDonorRequest
```bash
# Tester une latitude invalide
curl -X PUT http://localhost:8000/api/donors/1 \
  -d '{"latitude":200}'
# Devrait retourner erreur
```

- [ ] Authorization vérifiée
- [ ] Latitude entre -90 et 90
- [ ] Longitude entre -180 et 180

---

## 🔐 ÉTAPE 6: TESTER LES POLICIES

### Test DonorPolicy
```bash
# Utilisateur A ne peut pas modifier le profil de l'utilisateur B
curl -X PUT http://localhost:8000/api/donors/2 \
  -H "Authorization: Bearer TOKEN_USER_A"
# Devrait retourner 403 Forbidden
```

- [ ] Donneur peut modifier son profil
- [ ] Donneur ne peut pas modifier profil d'autres
- [ ] Admin peut modifier tous les profils

### Test BloodRequestPolicy
```bash
# Seul hôpital peut créer demande
curl -X POST http://localhost:8000/api/blood-requests \
  -H "Authorization: Bearer TOKEN_DONOR"
# Devrait retourner 403 Forbidden
```

- [ ] Hôpital peut créer demande
- [ ] Donneur ne peut pas créer demande
- [ ] Admin peut créer demande

### Test MessagePolicy
```bash
# Utilisateur A ne peut pas voir messages d'autres
curl -X GET http://localhost:8000/api/messages/other-user \
  -H "Authorization: Bearer TOKEN_USER_A"
# Devrait retourner 403 Forbidden
```

- [ ] Sender et receiver peuvent voir message
- [ ] Autres utilisateurs ne peuvent pas voir

---

## ⏰ ÉTAPE 7: TESTER LA LOGIQUE MÉTIER

### Test Délai Minimum Entre Dons
```bash
# Donneur qui a donné hier ne peut pas donner aujourd'hui
$donor = Donor::find(1);
$donor->update(['last_donation_date' => now()->subDays(30)]);
$result = $donor->isDonationEligible(); // false

$days = $donor->getDaysUntilEligible(); // 26
```

- [ ] isDonationEligible() retourne false si < 56 jours
- [ ] getDaysUntilEligible() retourne jours corrects
- [ ] Service lance exception si pas éligible

### Test Compatibilité Sanguine
```bash
// AB+ peut recevoir tous les types
$bloodRequest = BloodRequest::create(['blood_type' => 'AB+']);
$compatible = Donor::compatibleWith('AB+')->get();
// Devrait retourner tous les donneurs

// O+ ne peut recevoir que O+
$bloodRequest = BloodRequest::create(['blood_type' => 'O+']);
$compatible = Donor::compatibleWith('O+')->get();
// Devrait retourner seulement O+
```

- [ ] AB+ reçoit tous les types
- [ ] O+ reçoit seulement O+
- [ ] A+ reçoit O+ et A+
- [ ] A- reçoit O- et A-
- [ ] B+ reçoit O+ et B+
- [ ] B- reçoit O- et B-
- [ ] AB- reçoit O-, A-, B-, AB-

### Test Limite Demandes Urgentes
```bash
// Max 5 demandes critiques par jour par hôpital
$hospital = Hospital::find(1);
for ($i = 0; $i < 5; $i++) {
    BloodRequest::create([
        'hospital_id' => 1,
        'urgency' => 'critical'
    ]);
}

$result = $service->canMarkUrgent($bloodRequest);
// Devrait retourner ['can_mark' => false]
```

- [ ] Peut créer max 5 demandes critiques/jour/hôpital
- [ ] Service retourne raison si impossible

---

## 📊 ÉTAPE 8: TESTER LES RELATIONS

### Test Relations M2M
```bash
$bloodRequest = BloodRequest::find(1);
$donors = $bloodRequest->donors; // Devrait retourner donneurs

$donor = Donor::find(1);
$requests = $donor->bloodRequests; // Devrait retourner demandes
```

- [ ] BloodRequest.donors() fonctionne
- [ ] Donor.bloodRequests() fonctionne
- [ ] Pivot status récupérable

### Test Eager Loading
```bash
// Devrait charger tout en 2 requêtes
$donors = Donor::with('user', 'bloodRequests', 'donations')->get();
```

- [ ] Relations eager-loaded
- [ ] Pas de N+1 queries

---

## 🚀 ÉTAPE 9: TESTS AUTOMATISÉS

```bash
# Exécuter tous les tests
php artisan test

# Tester un fichier spécifique
php artisan test tests/Feature/DonationTest.php

# Avec coverage
php artisan test --coverage
```

- [ ] Tous les tests passent
- [ ] Coverage > 80%
- [ ] Pas de warnings

---

## 📈 ÉTAPE 10: MONITORING POST-DÉPLOIEMENT

### Vérifier les Logs
```bash
# Vérifier s'il n'y a pas d'erreurs
tail -f storage/logs/laravel.log
```

- [ ] Pas d'erreurs de migration
- [ ] Pas d'erreurs de requête
- [ ] Performances normales

### Vérifier les Métriques
- [ ] Temps de réponse < 200ms
- [ ] Taux d'erreur < 1%
- [ ] Database queries < 50

---

## ✅ DÉPLOIEMENT RÉUSSI?

- [ ] Toutes les migrations appliquées
- [ ] Toutes les Policies enregistrées
- [ ] Tous les contrôleurs mis à jour
- [ ] Toutes les routes fonctionnent
- [ ] Toutes les validations sont en place
- [ ] Toutes les autorisations fonctionnent
- [ ] Toute la logique métier fonctionne
- [ ] Tous les tests passent
- [ ] Pas d'erreurs en production
- [ ] Performance acceptable

---

## 🎉 CONCLUSION

Si toutes les cases sont cochées, le déploiement est **RÉUSSI** ✅

**Date du déploiement:** _________________  
**Responsable:** _________________  
**Status:** ☐ En cours ☐ Réussi ☐ Échoué  

---

## 📞 EN CAS DE PROBLÈME

1. Consulter `IMPLEMENTATION_GUIDE.md`
2. Vérifier les logs: `storage/logs/laravel.log`
3. Rouler back la migration: `php artisan migrate:rollback`
4. Contacter l'équipe de développement

---

**Créé le:** 2026-06-04  
**Version:** 1.0  
**Niveau de détail:** Complet
