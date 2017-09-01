Vue.use('vuex');

const store = new Vuex.Store({
    state: {
        name: '',
        title: '',
        checkout: '',
        accounts: [],
        account: [],
        showModal: false
    },
    getters: {
        accountListLength: (state) => {
            return state.accounts.length;
        },
        totalAccount: (state) => {
            return state.account.reduce((acc, x) => acc + (x.price * x.amount), 0);
        }
    },
    mutations: {
        updateName(state, name) {
            state.name = name;
        },
        updateTitle(state, title) {
            state.title = title;
        },
        updateCheckout(state, checkout) {
            state.checkout = checkout;
        },
        updateAccounts(state, accounts) {
            state.accounts = accounts;
        },
        updateAccount(state, account) {
            state.account = account;
        },
        updateShowModalFlag(state, showModal) {
            state.showModal = showModal;
        }
    },
    actions: {
        fetchAccounts: (context) => {
            axios.get('/api/accounts/date')
                 .then(response => {
                   context.commit('updateAccounts', response.data.accounts);
                 })
                 .catch(function (error) {
                   console.log(error);
                 });
        },
        createAccount: (context) => {
            axios.defaults.headers.common = {
                'X-Requested-With': 'XMLHttpRequest',
            };

            let accountData = null;

            if(context.state.name !== '') {
                accountData = {
                    name: context.state.name
                };
            }

            axios.post('/api/accounts/create', accountData)
                .then(function (response) {
                    if(response.data === 'success') {
                        swal('¡Correcto!', 'Cuenta registrada satisfactoriamente', 'success');
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    swal('Error', 'Esta cuenta no pudo ser registrada en el sistema', 'error')
                });
            // Clean form
            context.commit('updateName', '');
            // Update list
            context.dispatch('fetchAccounts');
        },
        showAccountDetails: (context, id) => {
            let accountId = parseInt(id);
            context.commit('updateShowModalFlag', true);
            axios.get('/api/accounts/'+accountId+'/details')
                 .then(response => {
                   context.commit('updateAccount', response.data.account);
                   context.commit('updateTitle', accountId);
                 })
                 .catch(function (error) {
                   console.log(error);
                 });
        },
        printAccount: (context, account) => {
            axios.get('/api/accounts/' + account.id)
                 .then(function (response) {
                   swal('¡Correcto!', 'Cuenta impresa satisfactoriamente', 'success');
                 })
                 .catch(function (error) {
                   console.log(error);
                 });
        },
        closeModal: (context) => {
            context.commit('updateShowModalFlag', false);
            context.commit('updateAccount', []);
            context.commit('updateTitle', '');
        }
    }
});

let accountForm = {
  template: `
  <section>
    <div v-if="accountListLength > 0">
      <div class="modal is-active" v-if="showModal">
        <div class="modal-background"></div>      
        <div class="modal-content">
          <div class="box">
            <h4>Cuenta {{ title }}</h4>
            <table class="table">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Cant.</th>
                  <th>Subt.</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th colspan="4">Total $</th>
                  <th>{{ totalAccount }}</th>
                </tr>
              </tfoot>
              <tbody>
                <tr v-for="(product, index) in account" v-if="product.price > 0" :key="product.id">
                  <td>{{ index + 1 }}</td>
                  <td>{{ product.name }}</td>
                  <td>{{ product.price }}</td>
                  <td>{{ product.amount }}</td>
                  <td>{{ product.price*product.amount }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <button class="modal-close is-large" aria-label="close" @click.prevent="closeModal"></button>      
      </div>
      <div class="field">
        <div class="control">
          <table class="table">
            <thead>
              <tr>
                <th>Cta.</th>
                <th>Mesa</th>
                <th>Apertura</th>
                <th>Imprimir</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accounts" :key="account.id">
                <td>
                  <a @click.prevent="showAccountDetails(account.id)">
                    {{ account.id }}
                  </a>
                </td>
                <td>{{ account.name }}</td>
                <td>{{ moment(account.checkin).format('h:mm:ss a') }}</td>
                <td>
                  <a class="link" @click.prevent="printAccount(account)">
                    <span class="icon">
                      <i class="fa fa-print"></i>
                    </span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>  
    </div>
    <div v-else>
      <h4>Debes crear una cuenta para comenzar</h4>
    </div>
    <div class="field">
      <label class="label">Mesa</label>
      <div class="control">
        <input class="input" type="text" placeholder="Por ejemplo: Mesa de mi cliente favorito" v-model="name">
      </div>
    </div>   
  </section>`,
  methods: {
    fetchAccounts () {
        this.$store.dispatch('fetchAccounts');
    },
    printAccount(account) {
        this.$store.dispatch('printAccount', account);
    },
    showAccountDetails(id) {
        this.$store.dispatch('showAccountDetails', id);
    },
    closeModal() {
        this.$store.dispatch('closeModal');
    }
  },
  computed: {
    accountListLength() {
      return this.$store.getters.accountListLength;
    },
    totalAccount() {
      return this.$store.getters.totalAccount;
    },
    title: {
      get () {
          return this.$store.state.title
      },
      set (value) {
          this.$store.commit('updateTitle', value)
      }
    },
    name: {
        get () {
            return this.$store.state.name
        },
        set (value) {
            this.$store.commit('updateName', value)
        }
    },
    checkout: {
        get () {
            return this.$store.state.checkout
        },
        set (value) {
            this.$store.commit('updateCheckout', value)
        }
    },
    accounts: {
      get () {
        return this.$store.state.accounts
      },
      set (value) {
        this.$store.commit('updateAccounts', value)
      }
    },
    account: {
      get () {
        return this.$store.state.account
      },
      set (value) {
        this.$store.commit('updateAccounts', value)
      }
    },
    showModal: {
      get () {
        return this.$store.state.showModal
      },
      set (value) {
        this.$store.commit('updateShowModalFlag', value)
      }
    }
  }
};

new Vue({
    delimiters: ['${', '}'],
    el: 'main',
    store,
    components: { accountForm },
    mounted() {
      this.fetchAccounts();
    },
    methods: {
      fetchAccounts () {
          this.$store.dispatch('fetchAccounts');
      },
      createAccount () {
          this.$store.dispatch('createAccount');
      }
    }
});
