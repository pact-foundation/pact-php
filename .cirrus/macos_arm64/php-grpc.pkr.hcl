packer {
  required_plugins {
    tart = {
      version = ">= 0.5.3"
      source  = "github.com/cirruslabs/tart"
    }
  }
}

variable "php_version" {
  type = string
}

source "tart-cli" "tart" {
  vm_base_name = "ghcr.io/cirruslabs/macos-ventura-base:latest"
  vm_name      = "php${var.php_version}-grpc"
  cpu_count    = 4
  memory_gb    = 8
  disk_size_gb = 70
  ssh_password = "admin"
  ssh_timeout  = "120s"
  ssh_username = "admin"
}

build {
  sources = ["source.tart-cli.tart"]
  provisioner "shell" {
    inline = [
      "brew install php@${var.php_version} composer protobuf",,
      "pecl install grpc"
    ]
  }
}
