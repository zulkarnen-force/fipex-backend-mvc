<?php


// [
//     {
//       "id": "",
//       "category_id": "",
//       "exhibition_id": "",
//       "name": "",
//       "description": "",
//       "total_points": "",
//       "members": [
//         {
//           "id_user": "",
//           "name": "",
//           "img_url": "",
//           "type": "[author/member]"
//         }
//       ],
//       "thumbnails": [
//         {
//           "id": "",
//           "url": ""
//         }
//       ]
//     }
//   ]
  
class ProductResponse {

    private $id;
    private $category_id;
    private $exhibition_id;
    private $name;
    private $description;
    private $total_points;
    private $members;
    private $thumbnails;
    private $products = array();


    public function __construct($products)
    {
        // foreach ($products as $prop => $value) {
        //     $this->{$prop} = $value;
        // }
        $this->products[] = $products;
    }
    public function setCustomerData(array $data) 
    {
        foreach ($data as $prop => $value) {
            $this->{$prop} = $value;
        }
    }


    public function setMembers($members) 
    {
        $this->$members = $members;
    }

    public function setThumbnail($thumbnails) 
    {
        $this->$thumbnails = $thumbnails;
    }





	/**
	 * @return mixed
	 */
	public function getProducts() {
		return $this->products;
	}
}