<template>
	<div class="container">
		<div class="row justify-content-center">
            <div class="col-md-10 col-lg-10">
	            <div align="center" class="card-header mb-1">
	            	<h3>Welcome to Sneakers Shop</h3>
	            </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div align="center" class="col-md-8 col-lg-8 mb-1">
            	<button @click="viewCart()" type="button" v-model="viewCartBtn" :class="btn">{{viewCartBtn}}</button>
            </div>
        </div>

        <div v-if="isViewCart" class="row justify-content-center">
            <div class="col-md-8 col-lg-8">
            	<div class="card card-header">
            		<h5>Your cart</h5>
            	</div>
            	<div class="card card-body" v-for="(item,index) in cartItems" v-bind:key="index">
            		<small><b>{{item.name}} - {{item.brand}} | Size: {{item.size}} - Quantity: {{item.quantity}}</b></small>
            	</div>
            </div>
            <div class="col-md-8 col-lg-8 mt-1 mb-1" align="center">
            	<button @click="checkout()" class="btn btn-success">Checkout</button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-8">
		        <form @submit.prevent="addToCart()" v-if="isPickASize" class="mb-1">
        			<small><b>{{cartItem.name}}</b></small> | 
					<small><b>{{cartItem.brand}}</b></small> | 
					<small><b>{{cartItem.price}}</b></small><br>

		        	<div class="form-group">
						<input type="number" min="36" max="45" class="form-control mt-1" placeholder="Size(in EU)" v-model="cartItem.size">
					</div>
					<div class="form-group">
						<input type="number" min="1" max="5" class="form-control" placeholder="Quantity" v-model="cartItem.quantity">
					</div>
					<button type="submit" class="btn btn-primary btn-block">Add to cart</button>
		        </form>
		    </div>
		</div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-header">
			        <div class="card card-body mb-2" v-for="sneaker in sneakers" v-bind:key="sneaker.id">
			        	<div class="row">
			        		<div class="col-md-4 col-sm-4"><img style="width:100%" v-bind:src="'cover_images/'+sneaker.cover_image">
			        		</div>
			        		<div class="col-md-8 col-sm-4">
			        			<small><b>{{sneaker.name}}</b></small> | 
								<small><b>{{sneaker.brand}}</b></small> | 
								<small><b>{{sneaker.price}}</b></small><br>	

								<div class="container">
									<button @click="pickASize(sneaker)" type="submit" class="btn btn-primary btn-block">Pick a size</button>
								</div>
			        		</div>
			        	</div>
						
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="row">
	            <div class="col-md-12 col-lg-12">
			        <nav aria-label="Page navigation example">
					  <ul class="pagination">
					    <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item"><a class="page-link" href="#" @click="fetchSneakers(pagination.prev_page_url)">Previous</a></li>
					    <li class="page-item disabled"><a class="page-link text-dark" href="#">Page {{pagination.current_page}} of {{pagination.last_page}}</a></li>
					    <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item"><a class="page-link" href="#" @click="fetchSneakers(pagination.next_page_url)">Next</a></li>
					  </ul>
					</nav>
				</div>
			</div>
		</div>

	</div>
</template>

<script>
	export default {
		props: ['userId'],

		data() {
			return {
				sneakers: [],
				sizes: [],
				pagination: {},
				cartItem: {
					user_id: '',
					sneaker_id: '',
					size: 42,
					quantity: 1,
					name: '',
					brand: '',
					price: '',
					cover_image: ''
				},
				cartItems: [],
				isPickASize: false,
				isViewCart: false,
				viewCartBtn: 'View Cart',
				btn: 'btn btn-success'
			}
		},

		created() {
			this.fetchSneakers();
			this.fetchSizes();
			console.log(this.userId);
		},

		methods: {
			fetchSneakers(page_url) {
				let vm = this;
				page_url = page_url || 'api/sneakers'
				fetch(page_url)
				.then(res => res.json())
				.then(res => {
					this.sneakers = res.data;
					vm.makePagination(res.meta, res.links);
				})
				.catch(err => console.log(err));
			},

			fetchSizes() {
				fetch('api/sizes')
				.then(res => res.json())
				.then(res => {
					this.sizes = res;
				})
				.catch(err => console.log(err));
			},

			makePagination(meta, links) {
				let pagination = {
					current_page: meta.current_page,
					last_page: meta.last_page,
					next_page_url: links.next,
					prev_page_url: links.prev
				}

				this.pagination = pagination;
			},

			addToCart() {
				this.cartItem.user_id = this.userId;
				var item = JSON.parse(JSON.stringify(this.cartItem));
				this.cartItems.push(item);
				this.isPickASize = false;
				alert('Added to cart!');
				// console.log(this.cartItems);
				// this.cartItem.size = 42;
				// this.cartItem.quantity = 1;
			},

			pickASize(sneaker) {
				this.cartItem.sneaker_id = sneaker.id;
				this.cartItem.name = sneaker.name;
				this.cartItem.brand = sneaker.brand;
				this.cartItem.price = sneaker.price;
				this.cartItem.cover_image = sneaker.cover_image;
				this.isPickASize = true;
				this.$nextTick(() => {
					window.scrollTo(0, 0);
				});
			},

			viewCart() {
				this.isViewCart = !this.isViewCart;
				if (this.viewCartBtn=='View Cart') {
					this.viewCartBtn = 'Hide Cart';
					this.btn = 'btn btn-secondary';
				}
				else {
					this.viewCartBtn = 'View Cart';
					this.btn = 'btn btn-success';
				}
			},

			checkout() {
				console.log(this.cartItems);
				confirm('Are you sure?');
				// let formData = new FormData();
		  // 		formData.append('cartitem', JSON.stringify(this.cartItem));
				fetch('api/checkout', {
					method: 'post',
					body: JSON.stringify(this.cartItems),
					headers: {
						'content-type': 'application/json'
					}
					
				})
				.then(res => res.json())
				.then(data => {
					alert('Successful payment!');
					this.isViewCart = false;
					this.cartItems = [];
					this.viewCartBtn = 'View Cart';
					this.btn = 'btn btn-success';
					this.fetchSneakers();
					this.fetchSizes();
					console.log(data);
				})
				.catch(err => console.log(err));
			}

		}
	}
</script>