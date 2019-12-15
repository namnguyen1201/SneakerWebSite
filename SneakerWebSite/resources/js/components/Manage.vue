<template>
	<div class="container">
		<div class="row justify-content-center">
            <div class="col-md-10 col-lg-10">
	            <div align="center" class="card-header mb-1">
	            	<h3>Admin Page</h3>
	            </div>
            </div>
        </div>

        <div class="row justify-content-center mb-2">
            <div class="col-md-8">
                <div class="card card-header">

                	<button @click="create()" class="btn btn-success mb-1" v-if="!isEdit && !isAddSize">New</button>

                    <form @submit.prevent="save()" v-if="isCreate" class="mb-1">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Name" v-model="createdSneaker.name">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Brand" v-model="createdSneaker.brand">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Price" v-model="createdSneaker.price">
						</div>
						<div class="form-group">
							<input name="cover_image" type="file" class="form-control-file" id="cover_image" ref="images" @change="addImage">
						</div>
						<button type="submit" class="btn btn-primary btn-block">Add</button>
					</form>

					<form @submit.prevent="edit(editedSneaker.id)" v-if="isEdit" class="mb-1">
						<div class="row">
			        		<div class="col-md-4 col-sm-4"><img style="width:100%" v-bind:src="'cover_images/'+editedSneaker.cover_image">
			        		</div>
			        		<div class="col-md-8 col-sm-8">
			        			<div class="form-group">
									<input type="text" class="form-control" placeholder="Name" v-model="editedSneaker.name">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Brand" v-model="editedSneaker.brand">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Price" v-model="editedSneaker.price">
								</div>
			        		</div>
			        	</div>
						<button type="submit" class="btn btn-primary btn-block mt-1">Save changes</button>
					</form>

					<form @submit.prevent="saveSize()" v-if="isAddSize" class="mb-1">
						<div class="row">
			        		<div class="col-md-4 col-sm-4"><img style="width:100%" v-bind:src="'cover_images/'+sizeAdded.cover_image">
			        		</div>
			        		<div class="col-md-8 col-sm-8">
				        		<div class="form-group">
									<input type="text" class="form-control" v-model="sizeAdded.name" disabled="">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" v-model="sizeAdded.brand" disabled="">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" v-model="sizeAdded.price" disabled="">
								</div>
							</div>
			        	</div>
						<div class="form-group">
							<input type="text" class="form-control mt-1" placeholder="Size(in EU)" v-model="sizeAdded.size">
						</div>
						<div class="form-group">
							<input type="number" min="1" class="form-control" placeholder="Quantity" v-model="sizeAdded.quantity">
						</div>
						<button type="submit" class="btn btn-primary btn-block">Save size</button>
					</form>

                </div>
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
			        			<small><b>Name: {{sneaker.name}}</b></small> | 
								<small><b>Brand: {{sneaker.brand}}</b></small> | 
								<small><b>Price: {{sneaker.price}}</b></small><br>
								<small><b>Available sizes</b></small>
								<div v-for="size in sizes" v-bind:key="size.id">
									<small v-if="size.sneaker_id==sneaker.id">Size {{size.size}}: {{size.quantity}}</small>
								</div>
			        		</div>
			        	</div>
						<hr>
						<button @click="addSize(sneaker)" class="btn btn-success mb-1">Add size</button>
						<button @click="editSneaker(sneaker)" class="btn btn-warning mb-1">Edit</button>
						<button @click="deleteSneaker(sneaker.id)" class="btn btn-danger">Delete</button>
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
		data() {
			return {
				sneakers: [],
				sizes: [],
				createdSneaker: {
					name: '',
					brand: '',
					price: '',
					cover_image: ''
				},

				editedSneaker: {
					id: '',
					name: '',
					brand: '',
					price: '',
					cover_image: ''
				},

				sizeAdded: {
					sneaker_id: '',
					size: '',
					quantity: '',
					name: '',
					brand: '',
					price: '',
					cover_image: ''
				},

				isEdit: false,
				isCreate: false,
				isAddSize: false,
				images: [],
				pagination: {}
			}
		},

		created() {
			this.fetchSneakers();
			this.fetchSizes();
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

			save() {
				if (this.validateSneakers(this.createdSneaker)) {
					let formData = new FormData()
			        formData.append('file', this.createdSneaker.cover_image);
			        formData.append('sneaker', JSON.stringify(this.createdSneaker));
					fetch('api/sneaker', {
						method: 'post',
						body: formData
					})
					.then(res => res.json())
					.then(data => {
						alert('Sneaker Added');
						this.fetchSneakers();
						this.isCreate = false;
						this.createdSneaker.name = '';
						this.createdSneaker.brand = '';
						this.createdSneaker.price = '';
						this.createdSneaker.cover_image = '';
						this.images = [];
						this.$refs.images.value = '';
					})
					.catch(err => console.log("Error"));
				}
			},

			edit(id) {
				if (this.validateSneakers(this.editedSneaker)) {
					fetch('api/sneaker/'+id, {
						method: 'put',
						body: JSON.stringify(this.editedSneaker),
						headers: {
							'content-type': 'application/json'
						}
					})
					.then(res => res.json())
					.then(data => {
						alert('Sneaker Edited');
						this.fetchSneakers();
						this.isEdit = false;
						this.editedSneaker.id = '';
						this.editedSneaker.name = '';
						this.editedSneaker.brand = '';
						this.editedSneaker.price = '';
						this.editedSneaker.cover_image = '';
					})
					.catch(err => console.log(err));
				}
			},

			editSneaker(sneaker) {
				this.editedSneaker.id = sneaker.id;
				this.editedSneaker.name = sneaker.name;
				this.editedSneaker.brand = sneaker.brand;
				this.editedSneaker.price = sneaker.price;
				this.editedSneaker.cover_image = sneaker.cover_image;
				this.isEdit = true;
				this.isCreate = false;
				this.isAddSize = false;
				this.$nextTick(() => {
					window.scrollTo(0, 0);
				});
			},

			addImage() {
				this.images = this.$refs.images.files;
				this.createdSneaker.cover_image = this.images[0];
			},

			deleteSneaker(id) {
				if (confirm("Are you sure?")) {
					fetch('api/sneaker/'+id, {
						method: 'delete'
					})
					.then(res => res.json())
					.then(data => {
						alert('Sneaker deleted');
						this.fetchSneakers();
					})
					.catch(err => console.log(err));
				}
			},

			addSize(sneaker) {
				this.sizeAdded.sneaker_id = sneaker.id;
				this.sizeAdded.name = sneaker.name;
				this.sizeAdded.brand = sneaker.brand;
				this.sizeAdded.price = sneaker.price;
				this.sizeAdded.cover_image = sneaker.cover_image;
				this.isAddSize = true;
				this.isCreate = false;
				this.isEdit = false;
				this.$nextTick(() => {
					window.scrollTo(0, 0);
				});
			},

			saveSize() {
				if (this.validateSize(this.sizeAdded)) {
					fetch('api/size', {
							method: 'post',
							body: JSON.stringify(this.sizeAdded),
							headers: {
								'content-type': 'application/json'
							}
						})
						.then(res => res.json())
						.then(data => {
							alert('Size Added');
							this.fetchSizes();
							this.fetchSneakers();
							this.sizeAdded.sneaker_id = '';
							this.sizeAdded.size = '';
							this.sizeAdded.quantity = '';
							this.sizeAdded.name = '';
							this.sizeAdded.brand = '';
							this.sizeAdded.price = '';
							this.sizeAdded.cover_image = '';
							this.isAddSize = false;
						})
						.catch(err => console.log(err));
				}
			},

			create() {
				this.isCreate = true;
				this.isEdit = false;
				this.isAddSize = false;
			},

			validateSneakers(sneaker) {
				if (!sneaker.name) {
					alert('Name is required!');
					return false;
				}
				else if (!sneaker.brand) {
					alert('Brand is required!');
					return false;
				}
				else if (!sneaker.price) {
					alert('Price is required!');
					return false;
				}
				else if (isNaN(sneaker.price) || sneaker.price <= 0) {
					alert('Price must be a positive number!');
				}
				else return true;
			},

			validateSize(size) {
				if (!size.size) {
					alert('Size is required!');
					return false;
				}
				else if (isNaN(size.size) || size.size <= 0) {
					alert('Size must be a positive number!');
					return false;
				}
				else if (!size.quantity) {
					alert('Quantity is required!');
					return false;
				}
				else return true;
			}
		}
	}
</script>