# Postman Collection

```
{
	"info": {
		"_postman_id": "8741dad8-de5e-44bc-86c3-ab7e5317f974",
		"name": "Treehole",
		"schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
	},
	"item": [
		{
			"name": "Index",
			"request": {
				"method": "GET",
				"header": [],
				"body": {},
				"url": {
					"raw": "{{treehole-host}}/api/v1/posts",
					"host": [
						"{{treehole-host}}"
					],
					"path": [
						"api",
						"v1",
						"posts"
					]
				}
			},
			"response": []
		},
		{
			"name": "Create",
			"request": {
				"method": "POST",
				"header": [
					{
						"key": "Content-Type",
						"value": "application/json"
					}
				],
				"body": {
					"mode": "raw",
					"raw": "{\n\t\"content\": \"This is a post request post created from postman.\"\n}"
				},
				"url": {
					"raw": "{{treehole-host}}/api/v1/posts",
					"host": [
						"{{treehole-host}}"
					],
					"path": [
						"api",
						"v1",
						"posts"
					]
				}
			},
			"response": []
		}
	]
}
```
