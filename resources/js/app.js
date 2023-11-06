import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'
import page from '@/components/page';
import utils from '@/components/utils';
import progressbar from '@/components/progressbar';
import main from './components/pages/main';
import leads from './components/pages/leads';
import messenger from './components/pages/messenger';
import overview from './components/pages/overview';
import agents from './components/pages/agents';
import emails from './components/pages/emails';

Alpine.plugin(persist)

window.Alpine = Alpine;

window.sleep = function(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
};


Alpine.store('app', {
    xpages: [],
    pageloading: false
});

Alpine.data('initPage', page);
Alpine.data('progressBar', progressbar);
Alpine.data('x_main', main);
Alpine.data('x_leads', leads);
Alpine.data('x_messenger', messenger);
Alpine.data('x_overview', overview);
Alpine.data('x_agents', agents);
Alpine.data('x_emails', emails);

Alpine.start();

