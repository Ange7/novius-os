(function($) {
    $.widget("ui.bloc", {
        // options par d�faut du widget
        // modifiables � la construction
        // mais aussi apr�s la construction du widget
        options: {
            title: 'Titre',
            togglable: true, // Variable indiquant si le bloc est ouvrable/fermable
            opened : true // Variable indiquant si le bloc est ouvert
        },

        // Variables internes
        uiBlocContainer: null, // contient l'objet jQuery du container du bloc
        uiBlocTitle: null, // contient l'objet jQuery du titre du bloc
        uiBlocTitleToggle: null, // contient l'objet jQuery du picto dans la barre de titre indiquant l'�tat d'ouverture/fermeture du bloc

        // La fonction _create est appel�e � la construction du widget
        // la variable d'instance this.element contient un objet jQuery
        // contenant l'�l�ment sur lequel porte le widget
        _create: function() {
            // On cr�e un container pour notre nouvel �l�ment d'UI
            // On lui ajoute la classe ui-widget qui doit �tre ajout�e � tout container de widget
            // On lui ajoute �galement la classe ui-widget-content qui doit �tre appliqu�e  � tout container de contenu de widget
            // La classe ui-corner-all arrondit les 4 angles de notre bloc
            this.uiBlocContainer = $('<div></div>')
                .addClass('ui-bloc ui-widget ui-widget-content ui-corner-all')
                .insertAfter(this.element);

            // On encapsule notre �l�ment initial dans notre nouveau container
            this.element.addClass('ui-bloc-content').appendTo(this.uiBlocContainer);
            this._title();

            if (this.options.togglable && !this.options.opened) {
                // Si le bloc est initialis� avec le param�tre opened � false, on ferme le bloc
                this._close();
            }
        },

        // Toutes les fonctions commen�ant par un underscore
        // sont des fonctions interne
        _title: function() {
            var self = this;

            // On ajoute la classe ui-widget-header qui doit �tre ajout�e � tout �l�ment titre de widget
            // La classe ui-corner-top arrondit les 2 angles supp�rieurs de notre bloc titre
            // L'�l�ment uiBlocTitle est ajout� au container et non plus � notre �l�ment de base
            self.uiBlocTitle = $('<h5></h5>').addClass('ui-bloc-title ui-widget-header ui-corner-top')
                .css('cursor', 'pointer') // On modifie le curseur au survol du titre
                .prependTo(this.uiBlocContainer);

            // On encapsule le texte du titre dans un span pour pouvoir le modifier
            $('<span></span>').text(self.options.title)
                .appendTo(self.uiBlocTitle);

            if (self.options.togglable) {
                // On ajoute l'�v�nement clic � notre titre si le bloc est ouvrable/fermable
                self.uiBlocTitle.click(function(event) {
                    self.toggle(event);
                    return false;
                });

                // On ajoute un span � notre titre
                // la classe ui-bloc-title-toggle va nous servir � placer le span � droite dans la barre de titre
                // la classe ui-icon associ�e � la classe ui-icon-pin-s
                // va transformer notre span en une ic�ne de t�te d'�pingle orient�e sud (vers le bas)
                self.uiBlocTitleToggle = $('<span></span>')
                    .addClass('ui-bloc-title-toggle ui-icon ui-icon-pin-s')
                    .appendTo(self.uiBlocTitle);
            }
        },

        // Les fonctions ne commen�ant pas par un underscore
        // sont des fonctions pouvant �tre appel�e de l'ext�rieur
        title: function(text) {
            if (typeof(text)!= 'undefined') {
                // la variable text a �t� pass�e en param�tre
                // Modification du texte
                // et ne pas oubli� de retourn� l'�l�ment (this.element)
                // pour rendre possible le chainage de fonction
                return this.uiBlocTitle.children('span:first').text(text);
            } else {
                // la variable text n'a pas �t� pass�e en param�tre
                // On retourne le texte actuellement contenu dans l'�l�ment
                return this.uiBlocTitle.children('span:first').text();
            }
        },

        toggle : function() {
            var self = this;

            //Si le bloc n'est pas ouvrable/fermable on sort tout de suite
            if (!self.options.togglable) {
                return self;
            }

            if (self.options.opened) {
                self._close();
            } else {
                self._open();
            }
            // On inverse la valeur de l'option opened
            self.options.opened = !self.options.opened;

            // On retourne l'instance du plugin pour pr�server le cha�nage des fonctions
            return self;
        },

        _close: function() {
            // On doit cacher tous les enfants du container sauf le titre
            this.uiBlocContainer.children().not(this.uiBlocTitle).hide();
            // L'ic�ne de la barre de titre devient une t�te d'�pingle orient�e vers l'ouest (west, donc vers la gauche)
            this.uiBlocTitleToggle.removeClass('ui-icon-pin-s').addClass('ui-icon-pin-w');
        },

        _open: function() {
            // On doit afficher tous les enfants du container
            this.uiBlocContainer.children().show();
            // L'ic�ne de la barre de titre devient une t�te d'�pingle orient�e vers le sud (vers le bas)
            this.uiBlocTitleToggle.removeClass('ui-icon-pin-w').addClass('ui-icon-pin-s');
        }
    });
})(jQuery);