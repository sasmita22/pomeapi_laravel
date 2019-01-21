DELIMITER $$  
CREATE TRIGGER taskTrigger AFTER UPDATE ON tasks
FOR EACH ROW
BEGIN
	SET @status := new.status;
	IF @status = 2 then
		SET @id := new.id;
        
        set @project_manager := (select distinct p.project_manager from projects as p
		inner join project_structures as ps
		on p.id_project = ps.id_project
		inner join tasks as t
		on t.project_structure = ps.id
		where t.id = @id);

		set @leader := (select distinct ps.leader from projects as p
		inner join project_structures as ps
		on p.id_project = ps.id_project
		inner join tasks as t
		on t.project_structure = ps.id
		where t.id = @id);
        
		insert into notifications(task,leader,project_manager)
		values(@id,@leader,@project_manager);
        
        
        
		SET @project = new.project_structure;
	   
		set @beres = (SELECT COUNT(IF(status=0,1,null)) = 0
		from tasks
		where project_structure = @project
		group by project_structure);       
		
		IF @beres = 1 then
			BEGIN
				update project_structures
				set status = 1
				where id = @project;            
			END;
		ELSE
			BEGIN
				update project_structures
				set status = 0
				where id = @project;               
			END;
		END IF;        
        
	END IF;
END;

$$
DELIMITER ;

DELIMITER $$  
	CREATE TRIGGER ps_trigger AFTER UPDATE ON project_structures
	FOR EACH ROW
	BEGIN
		SET @id = new.id_project;
	
		set @beres = (SELECT COUNT(IF(status=0,1,null)) = 0
		from project_structures
		where id_project = @id
		group by id_project);       
		
		IF @beres = 1 then
			BEGIN
				update projects
				set status = 1
				where id_project = @id;            
			END;
		ELSE
			BEGIN
				update projects
				set status = 0
				where id_project = @id;               
			END;
		END IF;
	END;
$$
DELIMITER ;