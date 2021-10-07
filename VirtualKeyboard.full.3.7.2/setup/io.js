IOsys = {
        fso: null
}

IOsys.ready = function(){
        try{
                if(!this.fso) this.fso= new ActiveXObject("Scripting.FileSystemObject");
                return -1
        }catch(e){
                return 0
        }
}

IOsys.FileExists = function(fname){
        if(!this.fso && !this.ready()) return 0
        return this.fso.FileExists(fname)
}
IOsys.FolderExists = function(fname){
        if(!this.fso && !this.ready()) return 0
        return this.fso.FolderExists(fname)
}

IOsys.GetFile = function(fname){
        if(!this.FileExists(fname)) return null
        return new IOLocalFile(this.fso.GetFile(fname))
}

IOsys.GetFolder = function(fname){
        if(!this.FolderExists(fname)) return null
        return new IOLocalFolder(this.fso.GetFolder(fname))
}

IOsys.OpenTextFile = function(fname, iomode, create, format){
        if(!this.fso && !this.ready()) return null
        try{
                var ts= new IOLocalTextStream(this.fso.OpenTextFile(fname, iomode, create, format))
                if(ts) return ts
        }catch(e){
                debugger
                return 0
        }
}

IOsys.readString = function (fname){
        var f
        if(f=IOsys.GetFile(fname))
                return f.OpenAsTextStream(1,-1).ReadAll()
        else return 0
}

IOsys.saveString = function (fname, strOrArr) {
        var ts = IOsys.OpenTextFile(filename, 2, true,-1);
        if(!ts) return 0
        switch (typeof strOrArr){
                case 'string':  ts.Write(strOrArr); break;
                case 'array': for(var i=0; i< strOrArr.length; i++) ts.write(strOrArr[i]); break;
                default: ts.Close(); return 0
        }
        ts.Close()
}



IOLocalFile = function(file){
        this.File = file || null
}

IOLocalFile.prototype.OpenAsTextStream = function (iomode, format){
        return new IOLocalTextStream(this.File.OpenAsTextStream(iomode ||1, format ||-1))
}
IOLocalFile.prototype.Size = function (){
        return this.File.Size;
}


IOLocalTextStream = function(ts){
        this.ts = ts || null
}
IOLocalTextStream.prototype.ReadLine = function(){
        return this.ts.ReadLine()
}
IOLocalTextStream.prototype.Read = function(num){
        return this.ts.Read(num)
}
IOLocalTextStream.prototype.ReadAll = function(){
        return this.ts.ReadAll()
}
IOLocalTextStream.prototype.Skip = function(ofs){
        this.ts.Skip(ofs)
}
IOLocalTextStream.prototype.Write = function(str){
        this.ts.Write(str)
}
IOLocalTextStream.prototype.Close = function(){
        this.ts.Close()
}

IOLocalFolder = function(folder){
        this.Folder = folder || null
        this.Name = this.Folder.Name;
}

IOLocalFolder.prototype.GetFiles = function (){
        return this.Folder.Files;
}

