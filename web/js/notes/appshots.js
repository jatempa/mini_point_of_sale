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
          <div v-if="selectedCategory === 2 && canAddGift">
            <div class="field">
              <label class="label">Tipo de servicio</label>
              <div class="control">
                  <div class="select">
                    <select v-model="kindService">
                      <option value="Pinneapple">1 Jarra de Piña</option>
                      <option value="Berry">1 Jarra de Berry</option>
                      <option value="Pack1">4 Naturales</option>
                      <option value="Pack2">3 Naturales y 1 Mineral</option>
                      <option value="Pack3">2 Naturales y 2 Minerales</option>
                      <option value="Pack4">1 Naturales y 3 Minerales</option>
                      <option value="Pack5">4 Minerales</option>
                    </select>
                  </div>
              </div>
            </div>
        
            <button class="button is-success" @click.prevent="addGiftService" style="margin-top: 8px;">
              <span class="icon is-normal">
                  <i class="fa fa-plus"></i>
              </span>
              <span>Agregar a lista</span>
            </button>
          </div>      
          <div v-else>
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
            <div v-if="selectedProduct > 0" style="margin-top: 8px;">        
            <div class="field">
              <label class="label">Cantidad</label>
              <div class="control">
                <input v-model="amount" class="input" type="number" min="0" max="100" placeholder="Introduce la cantidad del producto">
              </div>
            </div>
            
            <button class="button is-success" @click.prevent="addProduct"  style="margin-top: 8px;">
              <span class="icon is-normal">
                  <i class="fa fa-plus"></i>
              </span>
              <span>Agregar a lista</span>
            </button>
            </div>
          </div>
      </div>
    </section>                   
    `,
    data() {
        return {
            kindService: 0,
            canAddGift: true
        }
    },
    mounted() {
        this.fetchCategories();
        this.fetchProducts();
    },
    methods: {
        fetchCategories () {
            axios.get('/api/categories')
                .then(response => {
                    this.$store.commit('updateCategories', response.data.categories);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        fetchProducts () {
            axios.get('/api/products')
                .then(response => {
                    this.$store.commit('updateProducts', response.data.products);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        addProduct () {
            if (parseInt(this.$store.state.amount) > 0) {
                let product = {
                    amount: parseInt(this.$store.state.amount)
                };

                this.$store.state.products.filter((p) => p.id === parseInt(this.$store.state.selectedProduct))
                    .map(function(p) {
                        product.id = p.id;
                        product.name = p.name;
                        product.price = parseFloat(p.price);
                        product.total = parseFloat(product.amount * p.price)
                    });
                // Add to car products
                this.$store.commit('addCarProducts', product);
                // Clear
                this.$store.commit('updateSelectedCategory', 0);
                this.$store.commit('updateSelectedProduct', 0);
                this.$store.commit('updateAmount', 0);
                this.kindService = 0;
                this.canAddGift = true;
            } else {
                swal('Error', 'Debes ingresar una cantidad mínima de 1 producto para continuar', 'warning');
            }
        },
        addGiftService () {
            if (this.kindService === "Pinneapple") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 1);
                this.$store.commit('updateSelectedProduct', 13);
                this.addProduct();
            } else if (this.kindService === "Berry") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 1);
                this.$store.commit('updateSelectedProduct', 14);
                this.addProduct();
            } else if (this.kindService === "Pack1") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 4);
                this.$store.commit('updateSelectedProduct', 11);
                this.addProduct();
            } else if (this.kindService === "Pack2") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 3);
                this.$store.commit('updateSelectedProduct', 11);
                this.addProduct();
                this.$store.commit('updateAmount', 1);
                this.$store.commit('updateSelectedProduct', 12);
                this.addProduct();
            } else if (this.kindService === "Pack3") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 2);
                this.$store.commit('updateSelectedProduct', 11);
                this.addProduct();
                this.$store.commit('updateAmount', 2);
                this.$store.commit('updateSelectedProduct', 12);
                this.addProduct();
            } else if (this.kindService === "Pack4") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 1);
                this.$store.commit('updateSelectedProduct', 11);
                this.addProduct();
                this.$store.commit('updateAmount', 3);
                this.$store.commit('updateSelectedProduct', 12);
                this.addProduct();
            } else if (this.kindService === "Pack5") {
                this.canAddGift = false;
                this.$store.commit('updateAmount', 4);
                this.$store.commit('updateSelectedProduct', 12);
                this.addProduct();
            }
        }
    },
    computed: {
        getProductsByCategory () {
            return this.$store.state.products.filter((p) => p.category === this.$store.state.selectedCategory && p.category !== 3);
        },
        categories () {
            return this.$store.state.categories.filter((c) => c.id !== 3);
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
            axios.get('/api/accounts/date')
                .then(response => {
                    this.$store.commit('updateAccounts', response.data.accounts);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    computed: {
        getAccounts () {
            return this.$store.state.accounts;
        },
        getCarProducts () {
            return this.$store.state.carProducts;
        },
        carProductListLength () {
            return this.$store.state.carProducts.length;
        },
        totalCost () {
            return this.$store.state.carProducts.reduce((acc, x) => acc + (x.price * x.amount), 0);
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
        amount: 0
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
    }
});

new Vue({
    delimiters: ['${', '}'],
    el: 'main',
    store,
    components: { productList, productForm },
    mounted() {
        this.getLastNoteNumber();
    },
    methods: {
        getLastNoteNumber () {
            axios.get('/api/notes/lastNoteId')
                .then(response => {
                    response.data.map((n) => this.$store.state.lastNumberNote = parseInt(n.numberNote) + 1);
                    if (!(this.$store.state.lastNumberNote > 0)) {
                        this.$store.state.lastNumberNote = 1;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    this.$store.state.lastNumberNote = 1;
                });
        },
        createNote () {
            axios.defaults.headers.common = {
                'X-Requested-With': 'XMLHttpRequest',
            };

            if ((this.$store.state.carProducts.length > 0) && (parseInt(this.$store.state.selectedAccount) > 0) && (parseInt(this.$store.state.lastNumberNote) > 0)) {
                let noteData = {
                    selectedAccount: parseInt(this.$store.state.selectedAccount),
                    numberNote: parseInt(this.$store.state.lastNumberNote),
                    products: this.$store.state.carProducts
                };

                axios.post('/notes/create/shotwaiter', noteData)
                    .then(function (response) {
                        if(response.data === 'success') {
                            swal('¡Correcto!', 'Comanda registrada satisfactoriamente', 'success');
                            succ = true;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        swal('Error', 'Esta comanda no pudo ser registrada en el sistema', 'error')
                    });

                // Increment number note
                this.$store.commit('updateLastNumberNote');
                // Clean form
                this.cleanForm();
            } else {
                swal('Error', 'Verifica que hayas seleccionado una Cuenta y una serie de productos ', 'warning');
            }
        },
        cleanForm () {
            this.$store.commit('updateSelectedAccount',0);
            this.$store.commit('updateSelectedCategory', 0);
            this.$store.commit('updateSelectedProduct', 0);
            this.$store.commit('resetCarProducts');
        }
    }
});
