/**
 *  VPC
 */
#import {
#  to = aws_vpc.aws_vpc_wportal
#  id = "vpc-0a30b496e080ff1e4"
#}
resource "aws_vpc" "aws_vpc_wportal" {
  cidr_block           = "10.10.0.0/16"
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = {
    Name = "wportal-vpc"
  }
}

/**
 *  パブリックサブネット
 */
#import {
#  to = aws_subnet.public_0
#  id = "subnet-0578d70fd5df8d219"
#}
resource "aws_subnet" "public_0" {
  cidr_block              = "10.10.10.0/24"
  vpc_id                  = aws_vpc.aws_vpc_wportal.id
  map_public_ip_on_launch = true
  availability_zone       = "ap-northeast-1a"

  tags = {
    Name = "wportal-subnet-public-0"
  }
}
#import {
#  to = aws_subnet.public_1
#  id = "subnet-0ced52ff2c8b970a6"
#}
resource "aws_subnet" "public_1" {
  cidr_block              = "10.10.11.0/24"
  vpc_id                  = aws_vpc.aws_vpc_wportal.id
  map_public_ip_on_launch = true
  availability_zone       = "ap-northeast-1c"

  tags = {
    Name = "wportal-subnet-public-1"
  }
}

/**
 *  プライベートサブネット
 */
#import {
#  to = aws_subnet.private_0
#  id = "subnet-00cdd25fc7a991867"
#}
resource "aws_subnet" "private_0" {
  cidr_block              = "10.10.128.0/24"
  vpc_id                  = aws_vpc.aws_vpc_wportal.id
  availability_zone       = "ap-northeast-1a"
  map_public_ip_on_launch = false

  tags = {
    Name = "wportal-subnet-private-0"
  }
}
#import {
#  to = aws_subnet.private_1
#  id = "subnet-09ca2883e479b74dd"
#}
resource "aws_subnet" "private_1" {
  cidr_block              = "10.10.129.0/24"
  vpc_id                  = aws_vpc.aws_vpc_wportal.id
  availability_zone       = "ap-northeast-1c"
  map_public_ip_on_launch = false

  tags = {
    Name = "wportal-subnet-private-1"
  }
}

/**
 *  インターネットゲートウェイ
 */
#import {
#  to = aws_internet_gateway.igw
#  id = "igw-006ca5998f7660bb3"
#}
resource "aws_internet_gateway" "igw" {
  vpc_id = aws_vpc.aws_vpc_wportal.id
}

/**
 *  ルートテーブル.
 */
#import {
#  to = aws_route_table.public
#  id = "rtb-0f95bb582fbed3abd"
#}
resource "aws_route_table" "public" {
  vpc_id = aws_vpc.aws_vpc_wportal.id

  tags = {
    Name = "wportal-rt-public"
  }
}
#import {
#  to = aws_route_table.private_0
#  id = "rtb-0b69bef348b213971"
#}
resource "aws_route_table" "private_0" {
  vpc_id = aws_vpc.aws_vpc_wportal.id

  tags = {
    Name = "wportal-rt-private-0"
  }
}
#import {
#  to = aws_route_table.private_1
#  id = "rtb-0d95b51fe759c74af"
#}
resource "aws_route_table" "private_1" {
  vpc_id = aws_vpc.aws_vpc_wportal.id

  tags = {
    Name = "wportal-rt-private-1"
  }
}

/**
 *  ルート
 */
#import {
#  to = aws_route.public
#  id = "rtb-0f95bb582fbed3abd_0.0.0.0/0"
#}
resource "aws_route" "public" {
  route_table_id         = aws_route_table.public.id
  gateway_id             = aws_internet_gateway.igw.id
  destination_cidr_block = "0.0.0.0/0"
}
#import {
#  to = aws_route.private_0
#  id = "rtb-0b69bef348b213971_0.0.0.0/0"
#}
resource "aws_route" "private_0" {
  route_table_id         = aws_route_table.private_0.id
  nat_gateway_id         = aws_nat_gateway.nat_gateway_0.id
  destination_cidr_block = "0.0.0.0/0"
}
#import {
#  to = aws_route.private_1
#  id = "rtb-0d95b51fe759c74af_0.0.0.0/0"
#}
resource "aws_route" "private_1" {
  route_table_id         = aws_route_table.private_1.id
  nat_gateway_id         = aws_nat_gateway.nat_gateway_1.id
  destination_cidr_block = "0.0.0.0/0"
}

/**
 *  ルートテーブルの関連付け
 */
#import {
#  to = aws_route_table_association.public_0
#  id = "subnet-0578d70fd5df8d219/rtb-0f95bb582fbed3abd"
#}
resource "aws_route_table_association" "public_0" {
  subnet_id      = aws_subnet.public_0.id
  route_table_id = aws_route_table.public.id
}
#import {
#  to = aws_route_table_association.public_1
#  id = "subnet-0ced52ff2c8b970a6/rtb-0f95bb582fbed3abd"
#}
resource "aws_route_table_association" "public_1" {
  subnet_id      = aws_subnet.public_1.id
  route_table_id = aws_route_table.public.id
}
#import {
#  to = aws_route_table_association.private_0
#  id = "subnet-00cdd25fc7a991867/rtb-0b69bef348b213971"
#}
resource "aws_route_table_association" "private_0" {
  subnet_id      = aws_subnet.private_0.id
  route_table_id = aws_route_table.private_0.id
}
#import {
#  to = aws_route_table_association.private_1
#  id = "subnet-09ca2883e479b74dd/rtb-0d95b51fe759c74af"
#}
resource "aws_route_table_association" "private_1" {
  subnet_id      = aws_subnet.private_1.id
  route_table_id = aws_route_table.private_1.id
}

/**
*   Elastic IP Address
*/
#import {
#  to = aws_eip.nat_gateway_0
#  id = "eipalloc-0e8ef94affec5a5a9"
#}
resource "aws_eip" "nat_gateway_0" {
  domain     = "vpc"
  depends_on = [aws_internet_gateway.igw]

  tags = {
    Name = "wportal-eip-0"
  }
}
#import {
#  to = aws_eip.nat_gateway_1
#  id = "eipalloc-00508661b2b880101"
#}
resource "aws_eip" "nat_gateway_1" {
  domain     = "vpc"
  depends_on = [aws_internet_gateway.igw]

  tags = {
    Name = "wportal-eip-1"
  }
}

/**
*   NATゲートウェイ
*/
#import {
#  to = aws_nat_gateway.nat_gateway_0
#  id = "nat-06dd021d1295dc2b5"
#}
resource "aws_nat_gateway" "nat_gateway_0" {
  allocation_id = aws_eip.nat_gateway_0.id
  subnet_id     = aws_subnet.public_0.id
  depends_on    = [aws_internet_gateway.igw]

  tags = {
    Name = "wportal-nat-0"
  }
}
#import {
#  to = aws_nat_gateway.nat_gateway_1
#  id = "nat-008682f95b86381d4"
#}
resource "aws_nat_gateway" "nat_gateway_1" {
  allocation_id = aws_eip.nat_gateway_1.id
  subnet_id     = aws_subnet.public_1.id
  depends_on    = [aws_internet_gateway.igw]

  tags = {
    Name = "wportal-nat-1"
  }
}

