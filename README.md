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
			$products = $this->executeApiResponseToRC($products);
				
			return new ProductResourceCollection($products);
		}
		```
		
- **To packages to works correctly with Builder Query:**

    - Add next Trait:
	
		``` php
		use MrJmpl3\Laravel_Restful_Helper\Traits\ApiTrait;
		```
		
	- And use like next example to Resource Collection:
	
		``` php
		public function index() {
		    // Important! Don't close the query with get() or paginate()
		    // The second param is 'custom block filter' prevent to query override the builder select
		    
		    $products = Product::where('state', = , 1);
		    $products = $this->executeApiResponseFromBuilderToRC($products, ['state']);
		    
		    return new ProductResourceCollection($products);
		}
		```
		
	- And use like next example to Resource:
	
		``` php
		public function index() {
		    $product = Product::where('state', = , 1);
		    $product = $this->executeApiResponseFromBuilderToResource($product);
		    
		    return new ProductoResource($product);
		}
        ```
        
- **To packages to works correctly with Relations:**
    
    - In model, add array like next example:
        ``` php
        public $apiAcceptRelations = [
            'post'
        ];
    	```
    	
    	Where 'post' is the function name of relation
    		
    - In the API Resources, use the function embed

        ``` php
        $embedRequest = $this->embed($this);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            $this->mergeWhen(array_key_exists('post', $embedRequest), [
                'post' => $this->getPostResource($embedRequest),
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        
        private function getPostResource($embedRequest) {
            $postRelation = null;
            if (array_key_exists('post', $embedRequest)) {
                $postRelation = new PostResource($this->apiFieldsFromArrayToResource($this->post(), $embedRequest['post'])->first());
            }
            return $postRelation;
        }
    	```
    		
- **To transformers fields works with this package:**

	- In model, add array like next example:
	
		``` php
		public $apiTransforms = [
		    'id' => 'code'
		];
		```
		
		Where 'id' is the db column name , and 'code' is the column rename to response
	
	- In the API Resources, use the array $transforms
	
		``` php
		return [
			$this->apiTransforms['id'] => $this->id,
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
    	
    - Used the mergeWhen function like next example:
    
    	``` php
    	return [
    		$this->mergeWhen(is_null($this->id), [
    			$this->transforms['id'] => $this->id
    		]),
    		$this->mergeWhen(is_null($this->name), [
    			'name' => $this->name
    		]),
    	 ]
    	```
    		
- **To exclude fields in filter with this package:**

	- In model, add array like next example:
	
		``` php
		public $apiExcludeFilter = [
			'id'
		];
		```
		
		Where 'id' is the db column name to exclude
	
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
    	
    - **Embed:**
        
        - **Example:** /product?embed=relationfunction
        - **Example 2:** /user?embed=post.id,post.name
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email jmpl3.soporte@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
