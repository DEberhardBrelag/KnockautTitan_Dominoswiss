<?
class DominoSwissBase extends IPSModule {
	
	public function Create(){
		//Never delete this line!
		parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
		//You cannot use variables here. Just static values.
		$this->RegisterPropertyInteger("ID", 1);
		$this->RegisterPropertyBoolean("ShowLockLevel", true);
		$this->RegisterPropertyBoolean("EnableLockLevel", true);
		$this->RegisterPropertyBoolean("ShowSendingLockLevel", true);

		for ($i = 0; $i <= 3; $i++) {
			$this->RegisterVariableBoolean("LockLevel" . $i, $this->Translate("LockLevel ") . $i, "~Switch", 0);
		}

		if(!IPS_VariableProfileExists("BRELAG.Save")) {
			IPS_CreateVariableProfile("BRELAG.Save", 1);
			IPS_SetVariableProfileIcon("BRELAG.Save", "Lock");
			IPS_SetVariableProfileAssociation("BRELAG.Save", 0, $this->Translate("Save"), "", -1);
			IPS_SetVariableProfileAssociation("BRELAG.Save", 1, $this->Translate("Restore"), "", -1);
		}

		$this->RegisterVariableInteger("Saving", $this->Translate("Saving"), "BRELAG.Save", 0);
		$this->EnableAction("Saving");


		if(!IPS_VariableProfileExists("BRELAG.SendingOnLockLevel")) {
			IPS_CreateVariableProfile("BRELAG.SendingOnLockLevel", 1);
			IPS_SetVariableProfileIcon("BRELAG.SendingOnLockLevel", "Lock");
			IPS_SetVariableProfileAssociation("BRELAG.SendingOnLockLevel", 0, "0", "", -1);
			IPS_SetVariableProfileAssociation("BRELAG.SendingOnLockLevel", 1, "1", "", -1);
			IPS_SetVariableProfileAssociation("BRELAG.SendingOnLockLevel", 2, "2", "", -1);
			IPS_SetVariableProfileAssociation("BRELAG.SendingOnLockLevel", 3, "3", "", -1);
		}

		$this->RegisterVariableInteger("SendingOnLockLevel", $this->Translate("SendingOnLockLevel"), "BRELAG.SendingOnLockLevel", 0);
		$this->EnableAction("SendingOnLockLevel");


		$this->ConnectParent("{1252F612-CF3F-4995-A152-DA7BE31D4154}"); //DominoSwiss eGate
	}

	public function Destroy(){
		//Never delete this line!
		parent::Destroy();
		
	}

	public function ApplyChanges(){
		//Never delete this line!
		parent::ApplyChanges();

		for ($i = 0; $i <= 3; $i++) {
			if($this->ReadPropertyBoolean("EnableLockLevel")) {
				$this->EnableAction("LockLevel" . $i);
			} else {
				$this->DisableAction("LockLevel" . $i);
			}
			if($this->ReadPropertyBoolean("ShowLockLevel")) {
				IPS_SetHidden($this->GetIDForIdent("LockLevel" . $i), false);
			} else {
				IPS_SetHidden($this->GetIDForIdent("LockLevel" . $i), true);
			}
		}

		if($this->ReadPropertyBoolean("ShowSendingLockLevel")) {
			IPS_SetHidden($this->GetIDForIdent("SendingOnLockLevel"), false);
		} else {
			IPS_SetHidden($this->GetIDForIdent("SendingOnLockLevel"), true);
		}

		//Apply filter
		$this->SetReceiveDataFilter(".*\"ID\":\"". $this->ReadPropertyInteger("ID") ."\".*");
		
	}

	public function ReceiveData($JSONString) {

		//nothing to do
	
	}

	public function PulseUp(int $Priority){

		$this->SendCommand(1, 0  , $Priority);

	}

	public function PulseDown(int $Priority){

		$this->SendCommand(2, 0  , $Priority);

	}

	public function ContinuousUp(int $Priority){

		$this->SendCommand(3, 0  , $Priority);

	}

	public function ContinuousDown(int $Priority){

		$this->SendCommand(4, 0  , $Priority);

	}

	public function Stop(int $Priority){

		$this->SendCommand(5, 0  , $Priority);

	}

	public function Toggle(int $Priority){

		$this->SendCommand(6, 0  , $Priority);

	}

	public function Save(int $Priority){

		$this->SendCommand(15, 0  , $Priority);

	}

	//Support not wanted
	/*public function RestorePositionBoth(int $Priority){

		$this->SendCommand(16, 0  , $Priority);

	}*/

	public function RestorePosition(int $Priority){

		$this->SendCommand(23, 0  , $Priority);

	}

	public function LockLevelSet(int $Value) {

		$this->SendCommand(20, $Value , 3);

	}

	public function LockLevelClear(int $Value) {

		$this->SendCommand(21, $Value, 3);

	}

	protected function GetHighestLockLevel() {

		$result = 0;
		for ($i = 0; $i <= 3; $i++) {
			if (GetValue($this->GetIDForIdent("LockLevel". $i .""))) {
				$result = $i;
			}
		}

		return $result;
	}

	public function SendCommand(int $Command, int $Value, int $Priority) {

		$id = $this->ReadPropertyInteger("ID");
		return $this->SendDataToParent(json_encode(Array("DataID" => "{C24CDA30-82EE-46E2-BAA0-13A088ACB5DB}", "ID" => $id, "Command" => $Command, "Value" => $Value, "Priority" => $Priority)));

	}

}
?>