# Images

## Show

Show an image.

```
GET /api/v1/images/{imageId}
```

```
Response:

The image
```

## Store

Create an image.

```
POST /api/v1/images
```

```
Request:

{
	"image": "..." // the image as a file
}
```

```
Response:

{
    "id": 1
}
```
