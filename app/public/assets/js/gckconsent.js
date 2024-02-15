/*
* gckconsent - js cookie controller
*/
function gckconsent(init) {
    this.instance = init.instance;
    this.cookiename = init.cookiename || 'gckconsent' + Math.random();
    this.expiry = init.cookieexpiry || '-1';
    this.cookietypes = init.cookietypes || [e, f, a, m];
    this.cookietypetitles = init.cookietypetitles || [
        { name: 'e', title: 'Essenciais (Expiram ao fechar o navegador/aba)' },
        { name: 'f', title: 'Funcionais (Salvam suas preferências)' },
        { name: 'a', title: 'Analíticos (Anônimos, permitem avaliar como os visitante usam o site)' },
        { name: 'm', title: 'Marketing (Anônimos, permitem exibir sugestões relacionadas ao visitante)' }
    ];
    this.cookieinfourl = init.cookieinfourl || '/';
    this.cookiedmz = init.cookiedmz || [];
    this.cookielists = init.cookielists || { e: [], f: [], a: [], m: [] };
    this.getCookie = function () {
        var nameEQ = this.cookiename + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) {
                var val = c.substring(nameEQ.length, c.length);
                return (val) ? val : null;
            }
        }
        return null;
    },
    this.isTypeSet = function (type = '') {
        found = false;
        activetypes = this.getCookie() || 'e';
        looptypes = (activetypes.split(',') || ['e']);
        looptypes.some(c => {
            if (c.trim().toLowerCase() == type.trim().toLowerCase()) {
                found = true;
            }
        });
        return found;
    },
    this.setCookie = function (value, days = 0) {
        days = (days && days > 0) ? days : this.expiry;
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = this.cookiename + "=" + (value || "") + expires + "; path=/";
        if (document.querySelector('.gckconsent-setup') != undefined) {
            document.querySelector('.gckconsent-setup').style.opacity = 0;
            document.querySelector('.gckconsent-setup').remove();
        }
    },
    this.removeCookie = function (cookiename = this.cookiename) {
        var expires = "; expires=-1";
        document.cookie.split(';').some(c => {
            if (c.trim().indexOf(cookiename) != -1) {
                let namedcookie = c.trim().split('=')[0];
                document.cookie = namedcookie + "=" + expires + "; path=/";
            }
        });
    },
    this.showConsent = function () {
        if (document.querySelector('.gckconsent-setup') != undefined) {
            document.querySelector('.gckconsent-setup').style.opacity = 0;
            document.querySelector('.gckconsent-setup').remove();
        }
        cookielist = (this.getCookie() || 'e');
        preset = (cookielist.split(',') || []);
        if (document.getElementById('gckconsent_' + this.cookiename) == undefined) {
            var opts = '';
            for (i = 0; i < this.cookietypes.length; i++) {
                opts += `
            <li>
                <label class="switch">
                    <input id="slider-`+ this.cookietypes[i] + `" type="checkbox" value="` + this.cookietypes[i] + `" ` + ((preset.indexOf(this.cookietypes[i]) > -1 || this.cookietypes[i] == 'e') ? ' checked="checked"' : '') + ((this.cookietypes[i] == 'e') ? ' disabled="disabled"' : '') + ` />
                    <span class="slider round `+ ((this.cookietypes[i] == 'e') ? ' slider-e"' : '') +`"></span>
                </label>
                `+ this.cookietypetitles.find(item => item.name === this.cookietypes[i]).title + `
            </li>
            `;
            }
            var html = `
        <div id="gckconsent-prefs">
            <h3>Ajustar preferências de cookies</h3>
            <p class="explanation">Utilizamos Cookies para entender o comportamento de nossos visitantes e oferecer uma melhor experiência de navegação. Abaixo é possível verificar e indicar suas preferências pessoais para o uso de Cookies:</p>
            <h4>Preferências de Cookies</h4>
            <ul class="preflist">`+
                opts
                + `                
            </ul>
            <div class="controls">
                <button class="save" onclick="`+ this.instance + `.saveConsent()">Gravar preferências</button>
                <button onclick="location.href='`+ this.cookieinfourl + `'">O que são Cookies?</button>
            </div>
        </div>
        `;
            var innerhtml = '<div id="gckconsent_' + this.cookiename + '" class="gckconsent-show">' + html + '</div>';
            var fnxdiv = document.createElement('div');
            fnxdiv.innerHTML = innerhtml;
            while (fnxdiv.children.length > 0) {
                document.body.appendChild(fnxdiv.children[0]);
            }

            var posheight = (document.documentElement.scrollTop || document.body.scrollTop) + ((window.innerHeight - document.querySelector('#gckconsent-prefs').offsetHeight) / 2);
            document.getElementById('gckconsent-prefs').style.top = posheight + 'px';
            document.querySelector('body').style.overflow = 'hidden';
        }
    },
    this.saveConsent = function (days = this.expiry) {
        var opts = document.querySelectorAll('#gckconsent-prefs input[type=checkbox]:checked'), i;
        var value = [];
        for (i = 0; i < opts.length; i++)
            value.push(opts[i].value);
        for (addit = 0; addit < value.length; addit++) {
            if (
                value[addit].trim().toLowerCase() != 'e'
                && value[addit].trim().toLowerCase() != 'f'
            ) {
                if (value.toString().indexOf('f') < 0) value.push('f');
            }
        }
        //essential
        if (
            value.length < 1
            || value.toString().indexOf('e') < 0
        ){
            value.push('e');
        }

        days = (days && days > 0) ? days : this.expiry;
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = this.cookiename + "=" + (value.toString() || "") + expires + "; path=/";

        //turn off?
        const turnoff = this.cookietypes.filter(function(i){
            return value.indexOf(i) < 0;
        });

        for(ito=0;ito<turnoff.length;ito++){
            this.cookielists[turnoff[ito]].some(cookiename => {
                this.removeCookie(cookiename);
            });
        }
        document.getElementById('gckconsent_' + this.cookiename).style.opacity = 0;
        document.getElementById('gckconsent_' + this.cookiename).remove();
        location.reload();
    },
    this.showFlag = function () {
        const dmzlist = this.cookiedmz;
        dmzlist.push(this.cookieinfourl);
        const dmz = dmzlist.some(c => {
            if (location.href.indexOf(c.trim()) > -1) {
                return true;
            }
        });
        if (
            this.getCookie() === null
            && !dmz
        ) {
            var html = `
    <div id="gckconsent-flag">
    <h3><img src="/assets/image/svg/gpp_good.svg" alt="privacy icon" />Respeitamos sua privacidade</h3>
    <p>Utilizamos "cookies" para oferecer uma melhor experiência aos visitantes, personalizando conteúdos e corrigindo eventuais falhas de uso. Você pode aceitar o uso de todos os "cookies" sugeridos, ou configurar suas preferências abaixo:</p>
    <div class="controls">
        <button class="acceptall" onclick="`+ this.instance + `.setCookie(\'` + this.cookietypes.toString() + `\',`+ this.expiry +`)">Aceitar todos</button>
        <button class="prefs" onclick="`+ this.instance + `.showConsent()">Configurar preferências</button>
    </div>
    `;
            var innerhtml = (html) ? '<div id="boxck_' + this.cookiename + '" class="gckconsent-setup">' + html + '</div>' : '<div id="boxck_' + this.cookiename + '" class="fnxcksetup">' + basichtml + '</div>';
            var fnxdiv = document.createElement('div');
            fnxdiv.innerHTML = innerhtml;
            while (fnxdiv.children.length > 0) {
                document.body.appendChild(fnxdiv.children[0]);
            }
        }
    }
}

//setup
const cookieconsent = new gckconsent({
    instance: 'cookieconsent', //constant name
    cookiename: 'patriciaventuralgpd',
    cookieexpiry: '365', //days of persistence, -1 to session only
    cookietypes: ['e', 'f', 'a', 'm'], //e: essential(session),f: functional, a:analytics, m:marketing
    cookietypetitles: [
        { name: 'e', title: 'Essenciais (Expiram ao fechar o navegador/aba)' },
        { name: 'f', title: 'Funcionais (Salvam suas preferências no site)' },
        { name: 'a', title: 'Analíticos (Anônimos, permitem avaliar como você utiliza o site e promover melhorias)' },
        { name: 'm', title: 'Marketing (Anônimos, permitem exibir sugestões de conteúdos relacionados ao seu perfil)' }
    ],
    cookieinfourl: '/cookies', //url for site definitions about cookies
    cookiedmz: [
        'google',
        'lighthouse'
    ], //url requests containing these strings will not display consent popup
    cookielists: {
        e: [
            'patriciaventuraweb'
        ],
        f: [
            'patriciaventuralgpd'
        ],
        a: [
            '_ga',
            '_gid',
            '_gat',
            'AMP_TOKEN',
            '_gac_',
            '__utma',
            '__utmt',
            '__utmb',
            '__utmc',
            '__utmz',
            '__utmv',
        ],
        m: [
            '_gads'
        ]
    }
});
cookieconsent.showFlag();