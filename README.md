# LARAVEL RESTFUL HELPER

## Install

Via Composer

``` bash
$ composer require mrjmpl3/laravel-restful-helper
```

## Usage

This packages make queries depends of the request, like GraphQL.

### Requests

- **Filter data:** /product?column=value&column2=value2
- **Sort data:** /product?sort=-column1,column2
    - With the negative prefix = desc
    - Without the negative prefix = asc
- **Fields o Select data:** /product?fields=column1,column2,column3,column4
- **Paginate and Per Page:** /product?paginate=true&per_page=5
- **Embed:** /product?embed=relationfunction

### Code

#### To Collection

```
// Create a simple instance of model where you want apply the queries
$model = new Product();
$responseHelper = new ApiRestHelper($model);
          
// The method 'toCollection' return a collection with all data filtered
$response = $responseHelper->toCollection();
```
      
#### To Model

```
// Create a simple instance of model where you want apply the queries
$model = new Product();           
$responseHelper = new ApiRestHelper($model);
                
// The method 'toModel' return a model with all data filtered
$response = $responseHelper->toModel();
```
      
#### From Builder to Collection

```
// Important! Don't close the query with get() or paginate()
$query = Product::where('state', = , 1);
$responseHelper = new ApiRestHelper($query);
          
// The method 'toCollection' return a collection with all data filtered
$response = $responseHelper->toCollection();
```
      
#### Relations

- In model, add array like next example:

    ```
    public $apiAcceptRelations = [
        'post'
    ];
    ```
    Where 'post' is the function name of relation
                
- In the API Resources, use the function embed
    
    ```
    public function toArray($request) {
        $embed = (new ApiRestHelper)->getEmbed();
                
        return [
          'id' => $this->id,
          'name' => $this->name,
          $this->mergeWhen(array_key_exists('post', $embed), [
              'post' => $this->getPostResource($embed),
          ]),
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
        ];
    }
            
    private function getPostResource($embedRequest) {
      $postResource = NULL;
                
      if (array_key_exists('local', $embed)) {
          $postRelation = $this->local();                    
          $fieldsFromEmbed = (new ApiRestHelper($postRelation->getModel()))->getEmbedField('post');
                    
          if(!empty($fieldsFromEmbed)) {
              $postResource = new PostResource($postRelation->select($fieldsFromEmbed)->first());
          } else {
              $postResource = new PostResource($postRelation->first());
          }
      }
            
      return $postResource;
    }
    ```
#### Transformers

- In model, add array like next example:
	
```
public $apiTransforms = [
    'id' => 'code'
];
```
		
Where 'id' is the db column name , and 'code' is the column rename to response
	
- In the API Resources, use the array $apiTransforms
	
```
$apiHelper = new ApiRestHelper($this);

return [
    $apiHelper->getKeyTransformed('id') => $this->id,
    'name' => $this->name,
    'created_at' => $this->created_at,
    'updated_at' => $this->updated_at,
];
```

- To used fields in API Resources , You can combine with transformers fields
    
```
$apiHelper = new ApiRestHelper($this);

return [
    $this->mergeWhen($apiHelper->existInFields('id') && !is_null($this->id), [
        $this->transforms['id'] => $this->id
    ]),
    $this->mergeWhen($apiHelper->existInFields('name') && !is_null($this->name), [
        'name' => $this->name
    ]),
]
```

#### Exclude Fields in Filter

- In model, add array like next example:
	
```
public $apiExcludeFilter = [
    'id'
];
```
		
Where 'id' is the db column name to exclude
        
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email jmpl3.soporte@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
