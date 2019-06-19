<p align="center"><img src="https://github.com/senhungwong/treehole-server/blob/master/resources/assets/images/treehole-en.png" width="60%"></p>

<p align="center">
<a href="https://travis-ci.org/travis-ci/travis-web"><img src="https://travis-ci.org/senhungwong/treehole-server.svg?branch=master" alt="Build Status"></a>
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License: MIT"></a>
<a href="https://github.com/senhungwong/treehole-server/tags"><img src="https://img.shields.io/github/tag/senhungwong/treehole-server.svg" alt="GitHub tag"></a>
</p>

## Description

A server side secret burier. User can post anonymously their secrets to release the stress. Others can view these secrets from all over the world without knowing the person.

The site currently supports English and Simplified Chinese but the both language posts are in one thread.

The project is built with [treehole-client](https://github.com/senhungwong/treehole-client) as the front end client.

## Story

> They say in ancient times, people go into the mountains and the forests to find a tree hole, and tell the tree hole their secrets, then they seal the hole with some mud, and the secrets would stay there forever.

> 以前的人，心中如果有什么不可告人的秘密，他们会跑到山上，找一棵树，在树上挖一个洞，然后把秘密全说进去，再用泥巴把洞封上，那秘密就会永远留在那棵树里，没有人会知道。
> -- 梁朝伟 《花样年华》

## Demo

[Treehole](http://treehole.senhung.net)

**IMPORTANT: The back end is hosted on a free server. Please do not overwhalm the server by sending too many posts. Thank you!**

## Set Up

**Copy and edit `.env` file**

```bash
cp .env.example .env
```

**Start services**

```bash
docker-compose up --build -d
```

**Migrate**

```bash
docker-compose exec core php artisan migrate
```

## API

[Posts APIs](docs/APIs/posts.md)

[Images APIs](docs/APIs/images.md)

[Postman Collection](docs/APIs/postman-collection.md)

## Notes

Project icon is referenced from [文艺卡通小清新装饰插画广告设计](http://588ku.com/sucai/9691411.html)
