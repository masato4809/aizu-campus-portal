import * as React from 'react';
import {
  E_TARGET_TYPE,
  ETargetType,
  getTargetTypeName,
} from '@/script/Enum/Server/App/GoodJob/ETargetType';
import { IPropsBase } from '@/script/System/System';
import { Box } from '@mui/material';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InputField } from '@/script/Component/Form/InputField';
import { InputFieldMultiline } from '@/script/Component/Form/InputFieldMultiline';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import {
  IItemNumber,
  SelectItemNumber,
} from '@/script/Component/Form/SelectItemNumber';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { SelectTrnUserSingle } from '@/script/Pages/Common/Select/SelectTrnUserSingle';
import { SelectTrnDivisionSingle } from '@/script/Pages/Common/Select/SelectTrnDivisionSingle';
import { SelectTrnProjectSingle } from '@/script/Pages/Common/Select/SelectTrnProjectSingle';
import { IFormCreateValidationResult } from '@/script/Pages/GoodJob/New/FormCreateValidation';
import { InputError } from '@/script/Component/Form/InputError';

export interface IFormCreate {
  fromTargetType: ETargetType;
  fromTrnUserId: number;
  fromTrnDivisionId: number;
  fromTrnProjectId: number;
  fromOtherLabel: string;

  toTargetType: ETargetType;
  toTrnUserId: number;
  toTrnDivisionId: number;
  toTrnProjectId: number;
  toOtherLabel: string;

  title: string;
  content: string;
}
export const defaultInterface: IFormCreate = {
  fromTargetType: E_TARGET_TYPE.USER,
  fromTrnUserId: 0,
  fromTrnDivisionId: 0,
  fromTrnProjectId: 0,
  fromOtherLabel: '',

  toTargetType: E_TARGET_TYPE.USER,
  toTrnUserId: 0,
  toTrnDivisionId: 0,
  toTrnProjectId: 0,
  toOtherLabel: '',

  title: '',
  content: '',
};

interface IProps extends IPropsBase {
  formCreate: IFormCreate;
  validation: IFormCreateValidationResult;
  handleUpdate: (newValues: Partial<IFormCreate>) => void;
  handleSubmit: () => void;
}
export const FormCreate: React.FC<IProps> = ({
  sx,
  formCreate,
  validation,
  handleUpdate,
  handleSubmit,
}) => {
  const { authUser } = useCommonIndexContext();

  /**
   * 種別リスト作成.
   */
  const selectItemList: IItemNumber[] = React.useMemo(() => {
    return Object.values(E_TARGET_TYPE)
      .filter(v => v !== E_TARGET_TYPE.INVALID)
      .map(v => ({
        id: v,
        label: getTargetTypeName(v),
      }));
  }, []);

  /**
   * 送信元種別の変更時.
   */
  const handleChangeFromTarget = (value: number) => {
    handleUpdate({
      fromTargetType: value as ETargetType,
      fromTrnUserId: authUser.trnUser?.id || 0,
      fromTrnDivisionId: 0,
      fromTrnProjectId: 0,
      fromOtherLabel: '',
    });
  };

  /**
   * 送信先種別の変更時.
   */
  const handleChangeToTarget = (value: number) => {
    handleUpdate({
      toTargetType: value as ETargetType,
      toTrnUserId: 0,
      toTrnDivisionId: 0,
      toTrnProjectId: 0,
      toOtherLabel: '',
    });
  };

  /**
   * 送信元の選択・入力部分.
   */
  const nodeFromTarget = (): React.ReactNode => {
    switch (formCreate.fromTargetType) {
      case E_TARGET_TYPE.USER:
        return (
          <SelectTrnUserSingle
            sx={{
              width: responsiveSize(200),
            }}
            value={formCreate.fromTrnUserId}
            onChange={value => {
              handleUpdate({ fromTrnUserId: value });
            }}
            errors={validation.data.fromTrnUserId?.errors}
          />
        );
      case E_TARGET_TYPE.DIVISION:
        return (
          <SelectTrnDivisionSingle
            sx={{
              width: responsiveSize(200),
            }}
            value={formCreate.fromTrnDivisionId}
            onChange={value => {
              handleUpdate({ fromTrnDivisionId: value });
            }}
            errors={validation.data.fromTrnDivisionId?.errors}
          />
        );
      case E_TARGET_TYPE.PROJECT:
        return (
          <SelectTrnProjectSingle
            sx={{
              width: responsiveSize(200),
            }}
            value={formCreate.fromTrnProjectId}
            onChange={value => {
              handleUpdate({ fromTrnProjectId: value });
            }}
            errors={validation.data.fromTrnProjectId?.errors}
          />
        );
      case E_TARGET_TYPE.LABEL:
        return (
          <InputField
            sx={{
              width: responsiveSize(200),
            }}
            inputValue={formCreate.fromOtherLabel}
            onChange={value => {
              handleUpdate({ fromOtherLabel: value });
            }}
            errors={validation.data.fromOtherLabel?.errors}
          />
        );
      default:
        return null;
    }
  };

  /**
   * 送信先の選択・入力部分.
   */
  const nodeToTarget = (): React.ReactNode => {
    switch (formCreate.toTargetType) {
      case E_TARGET_TYPE.USER:
        return (
          <SelectTrnUserSingle
            sx={{
              width: responsiveSize(200),
            }}
            value={formCreate.toTrnUserId}
            onChange={value => {
              handleUpdate({ toTrnUserId: value });
            }}
            errors={validation.data.toTrnUserId?.errors}
          />
        );
      case E_TARGET_TYPE.DIVISION:
        return (
          <SelectTrnDivisionSingle
            sx={{
              width: responsiveSize(200),
            }}
            value={formCreate.toTrnDivisionId}
            onChange={value => {
              handleUpdate({ toTrnDivisionId: value });
            }}
            errors={validation.data.toTrnDivisionId?.errors}
          />
        );
      case E_TARGET_TYPE.PROJECT:
        return (
          <SelectTrnProjectSingle
            sx={{
              width: responsiveSize(200),
            }}
            value={formCreate.toTrnProjectId}
            onChange={value => {
              handleUpdate({ toTrnProjectId: value });
            }}
            errors={validation.data.toTrnProjectId?.errors}
          />
        );
      case E_TARGET_TYPE.LABEL:
        return (
          <InputField
            sx={{
              width: responsiveSize(200),
            }}
            inputValue={formCreate.toOtherLabel}
            onChange={value => {
              handleUpdate({ toOtherLabel: value });
            }}
            errors={validation.data.toOtherLabel?.errors}
          />
        );
      default:
        return null;
    }
  };

  return (
    <Box
      sx={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'start',
        gap: responsiveSpacing(4),
        ...sx,
      }}
    >
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>送信元</TypoText>
        <Box>
          <Box
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: responsiveSpacing(4),
            }}
          >
            <SelectItemNumber
              sxSelect={{
                width: responsiveSize(200),
              }}
              value={formCreate.fromTargetType}
              list={selectItemList}
              onChange={value => handleChangeFromTarget(value)}
            />
            {nodeFromTarget()}
          </Box>
          <InputError
            errors={[
              ...(validation.data.fromTrnUserId?.errors || []),
              ...(validation.data.fromTrnDivisionId?.errors || []),
              ...(validation.data.fromTrnProjectId?.errors || []),
              ...(validation.data.fromOtherLabel?.errors || []),
            ]}
          />
        </Box>
      </Box>
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>送信先</TypoText>
        <Box>
          <Box
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: responsiveSpacing(4),
            }}
          >
            <SelectItemNumber
              sxSelect={{
                width: responsiveSize(200),
              }}
              value={formCreate.toTargetType}
              list={selectItemList}
              onChange={value => handleChangeToTarget(value)}
            />
            {nodeToTarget()}
          </Box>
          <InputError
            errors={[
              ...(validation.data.toTrnUserId?.errors || []),
              ...(validation.data.toTrnDivisionId?.errors || []),
              ...(validation.data.toTrnProjectId?.errors || []),
              ...(validation.data.toOtherLabel?.errors || []),
            ]}
          />
        </Box>
      </Box>
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>タイトル</TypoText>
        <InputField
          id="form-good-job-create-title"
          sx={{
            width: '100%',
          }}
          fieldSx={{
            width: '100%',
          }}
          inputValue={formCreate.title}
          onChange={title => handleUpdate({ title })}
          visibleError
          errors={validation.data.title?.errors}
        />
      </Box>
      <Box
        sx={{
          width: '100%',
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>内容</TypoText>
        <InputFieldMultiline
          id="form-good-job-create-content"
          sx={{
            width: '100%',
          }}
          fieldSx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              resize: 'vertical',
            },
          }}
          rows={8}
          inputValue={formCreate.content}
          onChange={content => handleUpdate({ content })}
          visibleError
          errors={validation.data.content?.errors}
        />
      </Box>
      <ButtonGeneral
        sx={{
          marginLeft: 'auto',
          minWidth: responsiveSize(200),
        }}
        label="登録"
        onClick={handleSubmit}
      />
    </Box>
  );
};
