terraform {
  cloud {

    organization = "quick-wpd-human-tech"

    workspaces {
      name = "wportal"
    }
  }
}

#リージョンの設定
provider "aws" {
  region = "ap-northeast-1"

  default_tags {
    tags = {
      app = "wportal"
    }
  }
}