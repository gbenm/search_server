# API

## Search

### Params

| key | type | default |
| --- | ---- | ------- |
| query | string |     |
| page | int | 1       |
| pagesize | int | 10  |

### Example

`GET /search?query=java&page=1&pagesize=2`

```json
{
  "status": "success",
  "data": {
    "results": [
      {
        "title": "Java OOM Automatic Heap Dump File Name in K8S",
        "answer_count": 1,
        "username": "RGB314",
        "profile_picture_url": "https://www.gravatar.com/avatar/788b3466597b032a20965298e754a4bb?s=256&d=identicon&r=PG&f=1"
      },
      {
        "title": "Java for loop skipping a scanner and duplicating my printline",
        "answer_count": 0,
        "username": "Younis",
        "profile_picture_url": "https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg?sz=256"
      }
    ]
  }
}
```

## Stats

### Most Searched

#### Params
| key | type | default |
| --- | ---- | ------- |
| top | int  |   10    |
| from  | datetime | `2022-12-22` |
| until | datetime | now |

#### Example

`GET /stats`

```json
{
  "status": "success",
  "data": {
    "top": 10,
    "most_searched": [
      {
        "query": "java",
        "searches": 3
      },
      {
        "query": "javascript",
        "searches": 2
      },
      {
        "query": "coffeescript",
        "searches": 1
      }
    ]
  }
}
```

### Search Term Statistics

#### Params
| key | type | default |
| --- | ---- | ------- |
| query | string  |    |
| count | int  |  10   |
| exact | bool | false |
| from  | datetime | `2022-12-22` |
| until | datetime | now |

#### Examples

`GET /stats/p`

```json
{
  "status": "success",
  "data": {
    "stats": [
      {
        "query": "javascript",
        "searches": 2
      },
      {
        "query": "coffeescript",
        "searches": 1
      },
      {
        "query": "php",
        "searches": 1
      }
    ]
  }
}
```

`GET /stats/p?count=2`

```json
{
  "status": "success",
  "data": {
    "stats": [
      {
        "query": "javascript",
        "searches": 2
      },
      {
        "query": "coffeescript",
        "searches": 1
      }
    ]
  }
}
```

with exact=false (default)

`GET /stats/java`

```json
{
  "status": "success",
  "data": {
    "stats": [
      {
        "query": "java",
        "searches": 3
      },
      {
        "query": "javascript",
        "searches": 2
      }
    ]
  }
}
```

with exact=true

`GET /stats/java?exact=true`
```json
{
  "status": "success",
  "data": {
    "stats": [
      {
        "query": "java",
        "searches": 3
      }
    ]
  }
}

```
