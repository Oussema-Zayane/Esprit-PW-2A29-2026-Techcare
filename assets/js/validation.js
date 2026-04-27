// =====================================================
// CONTROLE DE SAISIE - Validation des formulaires
// JavaScript pur (pas de HTML5)
// =====================================================

// Validation du formulaire Fidélité (CREATE et EDIT)
function validateFideliteForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const titre = document.getElementById('titre');
    const description = document.getElementById('description');
    const total = document.getElementById('total');
    const dateGain = document.getElementById('dateGain');
    
    let isValid = true;
    let errorMessages = [];
    
    // Reset des erreurs
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.error-border').forEach(el => el.classList.remove('error-border'));
    
    // Validation du titre
    if (!titre || !titre.value.trim()) {
        showFieldError(titre, 'Le titre est obligatoire');
        isValid = false;
        errorMessages.push('Titre : champ obligatoire');
    } else if (titre.value.trim().length < 3) {
        showFieldError(titre, 'Le titre doit contenir au moins 3 caractères');
        isValid = false;
        errorMessages.push('Titre : minimum 3 caractères');
    } else if (titre.value.trim().length > 100) {
        showFieldError(titre, 'Le titre ne doit pas dépasser 100 caractères');
        isValid = false;
        errorMessages.push('Titre : maximum 100 caractères');
    } else if (!/^[a-zA-Z0-9\s\-\_\'àâäéèêëîïôöùûüç]+$/.test(titre.value.trim())) {
        showFieldError(titre, 'Caractères non autorisés (lettres, chiffres, espaces, tirets)');
        isValid = false;
        errorMessages.push('Titre : caractères non autorisés');
    }
    
    // Validation de la description
    if (!description || !description.value.trim()) {
        showFieldError(description, 'La description est obligatoire');
        isValid = false;
        errorMessages.push('Description : champ obligatoire');
    } else if (description.value.trim().length < 10) {
        showFieldError(description, 'La description doit contenir au moins 10 caractères');
        isValid = false;
        errorMessages.push('Description : minimum 10 caractères');
    } else if (description.value.trim().length > 500) {
        showFieldError(description, 'La description ne doit pas dépasser 500 caractères');
        isValid = false;
        errorMessages.push('Description : maximum 500 caractères');
    }
    
    // Validation du total (points)
    if (!total || total.value === '') {
        showFieldError(total, 'Le nombre de points est obligatoire');
        isValid = false;
        errorMessages.push('Points : champ obligatoire');
    } else if (isNaN(total.value) || parseInt(total.value) < 0) {
        showFieldError(total, 'Le nombre de points doit être un nombre positif');
        isValid = false;
        errorMessages.push('Points : doit être un nombre positif');
    } else if (parseInt(total.value) > 10000) {
        showFieldError(total, 'Le nombre de points ne peut pas dépasser 10 000');
        isValid = false;
        errorMessages.push('Points : maximum 10 000');
    }
    
    // Validation de la date
    if (!dateGain || !dateGain.value) {
        showFieldError(dateGain, 'La date est obligatoire');
        isValid = false;
        errorMessages.push('Date : champ obligatoire');
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(dateGain.value)) {
        showFieldError(dateGain, 'Format de date invalide (AAAA-MM-JJ)');
        isValid = false;
        errorMessages.push('Date : format invalide');
    }
    
    if (!isValid) {
        alert('❌ Erreurs de validation :\n\n- ' + errorMessages.join('\n- '));
        return false;
    }
    
    return true;
}

// Afficher une erreur sous un champ
function showFieldError(inputElement, message) {
    if (!inputElement) return;
    inputElement.classList.add('error-border');
    
    const errorDiv = document.createElement('small');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#ef4444';
    errorDiv.style.display = 'block';
    errorDiv.style.marginTop = '5px';
    errorDiv.style.fontSize = '0.75rem';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    // Supprimer l'ancienne erreur si elle existe
    const parent = inputElement.parentNode;
    const oldError = parent.querySelector('.error-message');
    if (oldError) oldError.remove();
    
    parent.appendChild(errorDiv);
}

// Compteur de caractères pour la description
function initCharCounter(textareaId, counterId, maxLength) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    
    if (!textarea || !counter) return;
    
    function updateCounter() {
        const length = textarea.value.length;
        counter.innerHTML = `${length} / ${maxLength} caractères`;
        
        if (length > maxLength) {
            counter.style.color = '#ef4444';
            textarea.classList.add('error-border');
        } else if (length < 10 && length > 0) {
            counter.style.color = '#f59e0b';
            textarea.classList.remove('error-border');
        } else {
            counter.style.color = '#8b9ab5';
            textarea.classList.remove('error-border');
        }
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Validation en temps réel
function initRealtimeValidation() {
    const titre = document.getElementById('titre');
    const description = document.getElementById('description');
    const total = document.getElementById('total');
    const dateGain = document.getElementById('dateGain');
    
    if (titre) {
        titre.addEventListener('input', function() {
            const error = this.parentNode.querySelector('.error-message');
            if (error) error.remove();
            this.classList.remove('error-border');
            
            if (this.value.trim().length > 0 && this.value.trim().length < 3) {
                showFieldError(this, 'Minimum 3 caractères');
            } else if (this.value.trim().length > 100) {
                showFieldError(this, 'Maximum 100 caractères');
            }
        });
    }
    
    if (description) {
        description.addEventListener('input', function() {
            const error = this.parentNode.querySelector('.error-message');
            if (error && this.value.trim().length >= 10) error.remove();
            this.classList.remove('error-border');
            
            if (this.value.trim().length > 0 && this.value.trim().length < 10) {
                showFieldError(this, 'Minimum 10 caractères');
            } else if (this.value.trim().length > 500) {
                showFieldError(this, 'Maximum 500 caractères');
            }
        });
    }
    
    if (total) {
        total.addEventListener('input', function() {
            const error = this.parentNode.querySelector('.error-message');
            if (error && parseInt(this.value) >= 0) error.remove();
            this.classList.remove('error-border');
            
            if (this.value !== '' && (isNaN(this.value) || parseInt(this.value) < 0)) {
                showFieldError(this, 'Nombre positif requis');
            } else if (parseInt(this.value) > 10000) {
                showFieldError(this, 'Maximum 10 000');
            }
        });
    }
    
    if (dateGain) {
        dateGain.addEventListener('change', function() {
            const error = this.parentNode.querySelector('.error-message');
            if (error && this.value) error.remove();
            this.classList.remove('error-border');
            
            if (this.value && !/^\d{4}-\d{2}-\d{2}$/.test(this.value)) {
                showFieldError(this, 'Format AAAA-MM-JJ');
            }
        });
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initRealtimeValidation();
    
    // Initialiser les compteurs de caractères
    initCharCounter('description', 'descCount', 500);
    
    // Attacher la validation au formulaire
    const fideliteForm = document.getElementById('fideliteForm');
    if (fideliteForm) {
        fideliteForm.addEventListener('submit', function(e) {
            if (!validateFideliteForm('fideliteForm')) {
                e.preventDefault();
            }
        });
    }
});