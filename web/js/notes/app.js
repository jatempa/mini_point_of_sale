Vue.use('vuex');

const store = new Vuex.Store({
    state: {
        lastNumberNote: 0,
        selectedAccount: 0,
        selectedCategory: 0,
        selectedProduct: 0,
        accounts: [],
        carProducts: [],
        categories: [],
        products: [],
        amount: 0,
        selectedGift: 0,
        flagAddGift: true
    },
    getters: {
        getProductsByCategory: (state) => {
            return state.products.filter((p) => p.category === state.selectedCategory && p.category !== 3);
        },
        filterCategories: (state) => {
            return state.categories.filter((c) => c.id !== 3);
        },
        isLicorBottle: (state) => {
            return state.categories.filter((c) => c.id === state.selectedCategory).map((c) => c.name === 'Botella')[0];
        },
        getAccounts: (state) => {
            return state.accounts;
        },
        getCarProducts: (state) => {
            return state.carProducts;
        },
        carProductListLength: (state) => {
            return state.carProducts.length;
        },
        totalCost: (state) => {
            return state.carProducts.reduce((acc, x) => acc + (x.price * x.amount), 0);
        }
    },
    mutations: {
        updateLastNumberNote(state) {
            state.lastNumberNote += 1;
        },
        updateAccounts(state, accounts) {
            state.accounts = accounts;
        },
        updateSelectedAccount(state, selectedAccount) {
            state.selectedAccount = selectedAccount;
        },
        updateSelectedCategory(state, selectedCategory) {
            state.selectedCategory = selectedCategory;
        },
        updateSelectedProduct(state, selectedProduct) {
            state.selectedProduct = selectedProduct;
        },
        updateSelectedGift(state, selectedGift) {
            state.selectedGift = selectedGift;
        },
        updateFlagAddGift(state, flagAddGift) {
            state.flagAddGift = flagAddGift;
        },
        updateProducts(state, products) {
            state.products = products;
        },
        updateCategories(state, categories) {
            state.categories = categories;
        },
        updateAmount(state, amount) {
            state.amount = amount;
        },
        addCarProducts(state, product) {
            state.carProducts.push(product);
        },
        resetCarProducts(state) {
            state.carProducts = [];
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
        fetchCategories: (context) => {
            axios.get('/api/categories')
                .then(response => {
                    context.commit('updateCategories', response.data.categories);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchProducts: (context) => {
            axios.get('/api/products')
                .then(response => {
                    context.commit('updateProducts', response.data.products);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        addProduct: (context) => {
            if (parseInt(context.state.amount) > 0) {
                let product = {
                    amount: parseInt(context.state.amount)
                };

                context.state.products.filter((p) => p.id === parseInt(context.state.selectedProduct))
                                      .map(function (p) {
                                          product.id = p.id;
                                          product.name = p.name;
                                          product.price = parseFloat(p.price);
                                          product.total = parseFloat(product.amount * p.price)
                                      });
                // Add to car products
                context.commit('addCarProducts', product);
                // Clear
                context.commit('updateSelectedCategory', 0);
                context.commit('updateSelectedProduct', 0);
                context.commit('updateAmount', 0);
            } else {
                swal('Error', 'Debes ingresar una cantidad mínima de 1 producto para continuar', 'warning');
                context.commit('updateSelectedCategory', 0);
                context.commit('updateSelectedGift', 0);
                context.commit('updateSelectedProduct', 0);
                context.commit('updateFlagAddGift', true);
            }
        },
        addGiftService: (context) => {
            if (context.state.flagAddGift) {
                context.commit('updateFlagAddGift', false);
                context.dispatch('addProduct');
            }

            switch (parseInt(context.state.selectedGift)) {
                case 1:
                    context.commit('updateAmount', 1);
                    context.commit('updateSelectedProduct', 13);
                    context.dispatch('addProduct');
                    break;
                case 2:
                    context.commit('updateAmount', 1);
                    context.commit('updateSelectedProduct', 14);
                    context.dispatch('addProduct');
                    break;
                case 3:
                    context.commit('updateAmount', 4);
                    context.commit('updateSelectedProduct', 11);
                    context.dispatch('addProduct');
                    break;
                case 4:
                    context.commit('updateAmount', 3);
                    context.commit('updateSelectedProduct', 11);
                    context.dispatch('addProduct');
                    context.commit('updateAmount', 1);
                    context.commit('updateSelectedProduct', 12);
                    context.dispatch('addProduct');
                    break;
                case 5:
                    context.commit('updateAmount', 2);
                    context.commit('updateSelectedProduct', 11);
                    context.dispatch('addProduct');
                    context.commit('updateAmount', 2);
                    context.commit('updateSelectedProduct', 12);
                    context.dispatch('addProduct');
                    break;
                case 6:
                    context.commit('updateAmount', 1);
                    context.commit('updateSelectedProduct', 11);
                    context.dispatch('addProduct');
                    context.commit('updateAmount', 3);
                    context.commit('updateSelectedProduct', 12);
                    context.dispatch('addProduct');
                    break;
                case 7:
                    context.commit('updateAmount', 4);
                    context.commit('updateSelectedProduct', 12);
                    context.dispatch('addProduct');
                    break;
            }

            context.commit('updateSelectedGift', 0);
            context.commit('updateFlagAddGift', true);
        },
        getLastNoteNumber: (context) => {
            axios.get('/api/notes/lastNoteId')
                .then(response => {
                    response.data.map((n) => context.state.lastNumberNote = parseInt(n.numberNote) + 1);
                    if (!(context.state.lastNumberNote > 0)) {
                        context.state.lastNumberNote = 1;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    context.state.lastNumberNote = 1;
                });
        },
        createNote: (context) => {
            axios.defaults.headers.common = {
                'X-Requested-With': 'XMLHttpRequest',
            };

            if ((context.state.carProducts.length > 0) && (parseInt(context.state.selectedAccount) > 0) && (parseInt(context.state.lastNumberNote) > 0)) {
                let noteData = {
                    selectedAccount: parseInt(context.state.selectedAccount),
                    numberNote: parseInt(context.state.lastNumberNote),
                    products: context.state.carProducts
                };

                axios.post('/api/notes/create', noteData)
                    .then(function (response) {
                        if (response.data === 'success') {
                            swal('¡Correcto!', 'Comanda registrada satisfactoriamente', 'success');
                            succ = true;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        swal('Error', 'Esta comanda no pudo ser registrada en el sistema', 'error')
                    });

                // Increment number note
                context.commit('updateLastNumberNote');
                // Clean form
                context.dispatch('cleanForm');
            } else {
                swal('Error', 'Verifica que hayas seleccionado una Cuenta y una serie de productos ', 'warning');
            }
        },
        cleanForm: (context) => {
            context.commit('updateSelectedAccount', 0);
            context.commit('updateSelectedCategory', 0);
            context.commit('updateSelectedGift', 0);
            context.commit('updateSelectedProduct', 0);
            context.commit('updateFlagAddGift', true);
            context.commit('resetCarProducts');
        }
    }
});

let productForm = {
    template: `
    <section>
      <div v-if="selectedAccount > 0" class="field" style="margin-top: 8px;">
        <label class="label">Selecciona un tipo de producto</label>
        <div class="control">
          <div class="select">
            <select v-model="selectedCategory">
              <option v-for="category in categories" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="selectedCategory > 0" class="field" style="margin-top: 8px;">
        <label class="label">Selecciona un producto</label>
        <div class="control">
          <div class="select">
            <select v-model="selectedProduct">
              <option v-for="product in getProductsByCategory" :value="product.id">
                {{ product.name }} ($ {{ product.price }})
              </option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="selectedProduct > 0" style="margin-top: 8px;">
        <div class="field">
          <label class="label">Cantidad</label>
          <div class="control">
            <input v-model="amount" class="input" type="number" min="0" max="100" placeholder="Introduce la cantidad del producto">
          </div>
        </div>
        
        <div v-if="isLicorBottle">
          <div class="field">
            <label class="label">Tipo de servicio</label>
            <div class="control">
              <div class="select">
                <select v-model="selectedGift">
                  <option value="0"></option>
                  <option value="1">1 Jarra de Piña</option>
                  <option value="2">1 Jarra de Berry</option>
                  <option value="3">4 Naturales</option>
                  <option value="4">3 Naturales y 1 Mineral</option>
                  <option value="5">2 Naturales y 2 Minerales</option>
                  <option value="6">1 Naturales y 3 Minerales</option>
                  <option value="7">4 Minerales</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div v-if="isLicorBottle && selectedGift > 0">
          <button class="button is-success" @click.prevent="addGiftService"  style="margin-top: 8px;">
            <span class="icon is-normal">
              <i class="fa fa-plus"></i>
            </span>
            <span>Agregar a lista</span>
          </button>
        </div>
        <div v-else-if="!isLicorBottle">
          <button class="button is-success" @click.prevent="addProduct"  style="margin-top: 8px;">
            <span class="icon is-normal">
              <i class="fa fa-plus"></i>
            </span>
            <span>Agregar a lista</span>
          </button>        
        </div>
      </div>
    </section>                   
    `,
    mounted() {
        this.fetchCategories();
        this.fetchProducts();
    },
    methods: {
        fetchCategories() {
            this.$store.dispatch('fetchCategories');
        },
        fetchProducts() {
            this.$store.dispatch('fetchProducts');
        },
        addProduct() {
            this.$store.dispatch('addProduct');
        },
        addGiftService() {
            this.$store.dispatch('addGiftService');
        }
    },
    computed: {
        getProductsByCategory() {
            return this.$store.getters.getProductsByCategory;
        },
        categories() {
            return this.$store.getters.filterCategories;
        },
        isLicorBottle() {
            return this.$store.getters.isLicorBottle;
        },
        selectedAccount: {
            get () {
                return this.$store.state.selectedAccount
            },
            set (value) {
                this.$store.commit('updateSelectedAccount', value)
            }
        },
        selectedCategory: {
            get () {
                return this.$store.state.selectedCategory
            },
            set (value) {
                this.$store.commit('updateSelectedCategory', value)
            }
        },
        selectedProduct: {
            get () {
                return this.$store.state.selectedProduct
            },
            set (value) {
                this.$store.commit('updateSelectedProduct', value)
            }
        },
        amount: {
            get () {
                return this.$store.state.amount
            },
            set (value) {
                this.$store.commit('updateAmount', value)
            }
        },
        selectedGift: {
            get () {
                return this.$store.state.selectedGift
            },
            set (value) {
                this.$store.commit('updateSelectedGift', value)
            }
        }
    }
};


let productList = {
    template: `
      <section>
        <div class="control">
          <label class="label">Selecciona una cuenta</label>
          <div class="select">
            <select v-model="selectedAccount">
              <option v-for="account in getAccounts" :value="account.id">
               Cta. {{ account.id }} {{ account.name }}
              </option>
            </select>
          </div>
        </div>
        <div v-if="carProductListLength > 0" class="field" style="margin-top: 8px;">
          <div class="label">Lista de productos</div>
          <div class="control">
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
                  <th>{{ totalCost }}</th>
                </tr>
              </tfoot>
              <tbody>
                <tr v-for="(product, index) in getCarProducts" :key="product.id">
                  <td>{{ index + 1 }}</td>
                  <td>{{ product.name }}</td>
                  <td>{{ product.price }}</td>
                  <td>{{ product.amount }}</td>
                  <td>{{ product.total }}</td>
                </tr>
              </tbody>
            </table>    
          </div>    
        </div>    
      </section>
  `,
    mounted() {
        this.fetchAccounts();
    },
    methods: {
        fetchAccounts() {
            this.$store.dispatch('fetchAccounts');
        }
    },
    computed: {
        getAccounts() {
            return this.$store.getters.getAccounts;
        },
        getCarProducts() {
            return this.$store.getters.getCarProducts;
        },
        carProductListLength() {
            return this.$store.getters.carProductListLength;
        },
        totalCost() {
            return this.$store.getters.totalCost;
        },
        selectedAccount: {
            get () {
                return this.$store.state.selectedAccount
            },
            set (value) {
                this.$store.commit('updateSelectedAccount', value)
            }
        }
    }
};

new Vue({
    delimiters: ['${', '}'],
    el: 'main',
    store,
    components: {productList, productForm},
    mounted() {
        this.getLastNoteNumber();
    },
    methods: {
        getLastNoteNumber() {
          this.$store.dispatch('getLastNoteNumber');
        },
        createNote() {
          this.$store.dispatch('createNote');
        },
        cleanForm() {
          this.$store.dispatch('cleanForm');
        }
    }
});
