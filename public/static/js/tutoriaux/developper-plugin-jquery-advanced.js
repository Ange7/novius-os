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

            // On ajoute la classe ui-widget-header qui doit �tre ajout�e � tout �l�ment titre de widget
            // La classe ui-corner-top arrondit les 2 angles supp�rieurs de notre bloc titre
            // L'�l�ment uiBlocTitle est ajout� au container et non plus � notre �l�ment de base
            this.uiBlocTitle = $('<h5></h5>').addClass('ui-bloc-title ui-widget-header ui-corner-top')
                .prependTo(this.uiBlocContainer);

            // On encapsule un SPAN dans le bloc titre pour y �crire le titre et pouvoir le modifier � posteriori
            $('<span></span>').appendTo(this.uiBlocTitle);

            // On ajoute un SPAN au bloc titre pour le picto indicateur de l'�tat d'ouverture / fermeture
            // Mais on le cache au cas o� la fonctionnalit� soit d�sactiv�e
            this.uiBlocTitleToggle = $('<span></span>')
                .addClass('ui-bloc-title-toggle ui-icon ui-icon-pin-s')
                .appendTo(this.uiBlocTitle)
                .hide();
        },

        // La fonction _init est appel�e � la construction ET � la r�initialisation du widget
        _init : function() {
            var self = this;

            // On renseigne le texte du titre avec la valeur pr�sente dans les options
            self.uiBlocTitle.children('span:first').text(self.options.title);

            // On enl�ve l'�v�nement click sur le bloc titre
            // En cas de r�initialisation, cela �vite d'ajouter un nouvel �v�nement click
            // � notre titre qui en a d�j� potentiellement un
            self.uiBlocTitle.unbind('click');

            if (self.options.togglable) {
                // On ajoute l'�v�nement click au bloc titre si le bloc est ouvrable / fermable
                self.uiBlocTitle.click(function(event) {
                    // Si le widget est disabled, il ne se passe rien au click
                    if (!self.options.disabled) {
                        self.toggle(event);
                        return false;
                    }
                }).css('cursor', 'pointer'); // On modifie le curseur au survol du titre

                // On affiche le picto indicateur de l'�tat d'ouverture / fermeture
                // pr�c�demment cr�e dans _create
                self.uiBlocTitleToggle.show();

                if (!self.options.opened) {
                    // Si le bloc est initialis� avec le param�tre opened � false, on ferme le bloc
                    self._close();
                } else {
                    // Si le bloc est initialis� avec le param�tre opened � true, on ouvre le bloc
                    self._open();
                }
            } else {
                // Le bloc n'est pas togglable
                // On r�initialise le curseur au survol du titre
                self.uiBlocTitle.css('cursor', 'auto');
                // On cache le picto indicateur de l'�tat d'ouverture / fermeture
                self.uiBlocTitleToggle.hide();
                // On ouvre le bloc
                self._open();
            }
        },

        // La fonction destroy ram�ne l'�l�ment du DOM, sur lequel est bas� notre widget,
        // dans l'�tat o� il �tait avant la cr�ation du widget.
        // Elle d�fait ce que _create a fait
        destroy: function() {
            // On r�affiche l'�l�ment �ventuellement cach�
            // On enl�ve les classes css propres au widget
            // Et on sort l'�l�ment du container
            this.element.show()
                .removeClass('ui-bloc-content')
                .insertBefore(this.uiBlocContainer);

            // On d�truit le container
            // Ce qui d�truit par ricochet tous les autres �l�ments cr��s par notre widget
            this.uiBlocContainer.remove();

            // On appelle la m�thode originale du framework
            // Elle supprime l'instance du widget qui a �t� stock� en data dans l'�l�ment
            $.Widget.prototype.destroy.apply(this);

            return this;
        },

        // Surcharge de la m�thode _setOption qui est appel�e par la m�thode option
        // qui permet de modifier des options de notre widget
        _setOption: function(key, value){
            var self = this;

            // On appelle la m�thode originale du framework qui modifie le tableau d'options
            $.Widget.prototype._setOption.apply(self, arguments);

            if ($.inArray(key, ['title', 'togglable', 'opened']) != -1) {
                // Si l'option modifi�e est une des 3 options title, togglable, opened
                // On appelle la m�thode d'initialisation
                self._init();
            } else if (key === 'disabled') {
                // L'option disabled a �t� modifi�e
                // On ajoute ou supprime, en fonction du cas, la classe ui-state-disabled au container
                // Dans le framework css de jQuery UI, la classe ui-state-disabled grise un �l�ment
                if (value) {
                    this.uiBlocContainer.addClass('ui-state-disabled');
                } else {
                    this.uiBlocContainer.removeClass('ui-state-disabled');
                }
            }
        },

        toggle : function() {
            var self = this;

            //Si le bloc n'est pas ouvrable/fermable on sort tout de suite
            if (!self.options.togglable) {
                return self;
            }

            if (self.options.opened) {
                // Si l'�v�nement beforeClose retourne false, on arr�te la fermeture
                if (false === this._trigger('beforeClose')) {
                    return false;
                }

                self._close();

                // Envoi du signal de fermeture
                // _trigger accepte 3 param�tres, les deux derniers �tant optionnels :
                // - le nom de l'�v�nement
                // - l'objet �v�nement
                // - des donn�es additionnelles envoy�es aux fonctions interceptant l'�v�nement
                this._trigger('close');
            } else {
                // Si l'�v�nement beforeOpen retourne false, on arr�te l'ouverture
                if (false === this._trigger('beforeOpen')) {
                    return false;
                }

                self._open();

                // Envoi du signal d'ouverture
                this._trigger('open');
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
