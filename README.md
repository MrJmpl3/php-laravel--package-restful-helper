# LARAVEL RESTFUL HELPER

## Install

Via Composer

``` bash
$ composer require mrjmpl3/laravel-restful-helper
```

## Usage

- **To packages to works correctly:**

	- Add next Trait:
	
		``` php
		use MrJmpl3\Laravel_Restful_Helper\Traits\ApiTrait;
		```
	- And use like next example to Resource Collection:
	
		``` php
		public function index() {
			$products = new Product();
			$products = $this->executeApiResponse($products);
				
			return new ProductResourceCollection($products);
		}
		```
	- And use like next example to Resource:
	
		``` php
		public function index() {
			$product = new Product();
			$product = $this->apiFieldsOnlyModel($product);
				
			return new ProductoResource($product);
		}
		```

- **To transformers fields works with this package:**

	- In model, add array like next example:
	
		``` php
		public $transforms = [
			'id' => 'code'
		];
		```
		
		Where 'id' is the db column name , and 'code' is the column rename to response
	
	- In the API Resources, use the array $transforms
	
		``` php
		return [
			$this->transforms['id'] => $this->id,
			'name' => $this->name,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
		```
		
- **To used fields in API Resources , You can combine with transformers fields**

	- Add next Trait:
	
    	``` php
    	use MrJmpl3\Laravel_Restful_Helper\Traits\ApiTrait;
    	```
    	
    - Used the mergeWhen and existsInFields function like next example:
    
    	``` php
    	return [
    		$this->mergeWhen($this->existsInApiFields($this,'id'), [
    			$this->transforms['id'] => $this->id
    		]),
    		$this->mergeWhen($this->existsInApiFields($this,'name'), [
    			'name' => $this->name
    		]),
    	 ]
    	```
    		
- **To request:**

	- **Filter data:** 
	
		- **Example:** /product?column=value&column2=value2
		
	- **Sort data:**
	
		- **Example:** /product?sort=-column1,column2
		
			- With the negative prefix = desc
            - Without the negative prefix = asc
            
    - **Fields o Select data:**
    
    	- **Example:** /product?fields=column1,column2,column3,column4
    	
    - **Paginate and Per Page:**
    
    	- **Example:** /product?paginate=true&per_page=5
    	
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email jmpl3.soporte@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
