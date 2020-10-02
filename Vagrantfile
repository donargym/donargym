# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  config.vm.define "donar.markmeijerman.loc", primary: true do |config|
    config.vm.box = "f500/debian-buster64"
    config.vm.network :private_network, ip: "192.168.30.105"

    config.ssh.insert_key = false
    config.ssh.forward_agent = true

    nfsPath = "."
    if Dir.exist?("/System/Volumes/Data")
        nfsPath = "/System/Volumes/Data" + Dir.pwd
    end
    config.vm.synced_folder nfsPath, "/vagrant"

    config.vm.provider :virtualbox do |v|
      v.name = "donargym"
      v.customize ["modifyvm", :id, "--cpus", "1"]
      v.customize ["modifyvm", :id, "--memory", "2048"]
      v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    end

    config.vm.provision "ansible" do |ansible|
      ansible.compatibility_mode = "2.0"
      ansible.inventory_path     = "ansible/hosts"
      ansible.playbook           = "ansible/provision/provision.yml"
      ansible.limit              = "develop"
      ansible.host_key_checking  = "False"
    end
  end
end
