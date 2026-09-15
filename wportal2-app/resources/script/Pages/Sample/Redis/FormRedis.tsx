import * as React from 'react';
import { FormControl } from '@mui/material';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { E_BUTTON_TYPE } from '@/script/Component/Button/EButtonType';
import { IPropsBase } from '@/script/System/System';
import { IFormRedisValidationResult } from '@/script/Pages/Sample/Redis/FormRedisValidation';

export interface IFormRedis {
  storeValue: string;
}

interface IProps extends IPropsBase {
  loading: boolean;
  formRedis: IFormRedis;
  validation: IFormRedisValidationResult;
  handleUpdate: (newValues: Partial<IFormRedis>) => void;
  handleSaveGraphQL: () => void;
}
export const FormRedis: React.FC<IProps> = ({
  sx,
  loading,
  formRedis,
  validation,
  handleUpdate,
  handleSaveGraphQL,
}) => {
  return (
    <FormControl
      sx={{
        ...sx,
      }}
    >
      <InputField
        inputValue={formRedis.storeValue}
        onChange={storeValue => handleUpdate({ storeValue })}
        placeHolder="入力してください"
        errors={validation.data.storeValue?.errors}
        visibleError
      />
      <ButtonGeneral
        sx={{
          marginTop: '20px',
        }}
        buttonType={E_BUTTON_TYPE.CONTAINED_PRIMARY}
        onClick={handleSaveGraphQL}
        label="GraphQLで保存"
        disabled={loading}
      />
    </FormControl>
  );
};
