(function($) {
    // Pour cr�er mon plugin il suffit d'appeller la m�thode $.widget
    // Le 1er param�tre est le nom de mon plugin pr�fix� par ui. (le namespace de jQuery UI)
    // Le 2eme param�tres est un objet json de param�trage du plugin
    $.widget("ui.bloc", {
        // options par d�faut du widget
        // modifiables � la construction
        // mais aussi apr�s la construction du widget
        options: {
            title: 'Titre'
        },

        // Une variable interne
        // contient l'objet jQuery du titre du bloc
         uiBlocTitle: null,

        // La fonction _create est appel�e � la construction du widget
        // la variable d'instance this.element contient un objet jQuery
        // contenant l'�l�ment sur lequel porte le widget
        _create: function() {
            this.element.addClass('uiBloc');
            this._title() ;
        },

        // Toutes les fonctions commen�ant par un underscore
        // sont des fonctions internes
         _title: function() {
            this.uiBlocTitle = $('<h5></h5>').text(this.options.title).prependTo(this.element);
         },

        // Les fonctions ne commen�ant pas par un underscore
        // sont des fonctions pouvant �tre appel�es de l'ext�rieur
        title: function(text) {
            if (typeof(text)!= 'undefined') {
                // la variable text a �t� pass�e en param�tre
                // Modification du texte
                // et ne pas oublier de retourner l'�l�ment (this.element)
                // pour rendre possible le chainage de fonction
                return this.uiBlocTitle.text(text);
            } else {
                // la variable text n'a pas �t� pass�e en param�tre
                // On retourne le texte actuellement contenu dans l'�l�ment
                return this.uiBlocTitle.text();
            }
        }
    });
})(jQuery);