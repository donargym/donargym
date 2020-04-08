# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

    config.vm.define "donar.markmeijerman.loc", primary: true do |donar|
        donar.vm.box = "f500/debian-stretch64"
        donar.vm.network :private_network, ip: "192.168.20.105"

        nfsPath = "."
        if Dir.exist?("/System/Volumes/Data")
            nfsPath = "/System/Volumes/Data" + Dir.pwd
        end
        donar.vm.synced_folder ".", "/vagrant", disabled: true
        donar.vm.synced_folder nfsPath, "/vagrant/donargym", nfs: true

        donar.ssh.insert_key = false
        donar.ssh.forward_agent = true

        donar.vm.provider :virtualbox do |v|
          v.customize ["modifyvm", :id, "--cpus", "1"]
          v.customize ["modifyvm", :id, "--memory", "2048"]
          v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
          v.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
        end

        donar.vm.provision :ansible do |ansible|
            ansible.compatibility_mode = "2.0"
            ansible.inventory_path     = "ansible/hosts"
            ansible.playbook           = "ansible/provision/provision.yml"
            ansible.limit              = "develop"
        end
    end

end



